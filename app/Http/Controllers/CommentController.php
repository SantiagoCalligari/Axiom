<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\CommentResourceCollection;
use App\Models\Comment;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Career;
use App\Models\University;
use App\Models\Vote;
use Illuminate\Http\JsonResponse as HttpJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommentController extends Controller
{
    /**
     * Display a paginated list of the exam's top-level comments.
     *
     * @param  \App\Models\University  $university
     * @param  \App\Models\Career  $career
     * @param  \App\Models\Subject  $subject
     * @param  \App\Models\Exam  $exam
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\CommentResourceCollection
     */
    public function index(University $university, Career $career, Subject $subject, Exam $exam, Request $request): CommentResourceCollection
    {
        // Ensure the exam belongs to the correct university, career, and subject
        if ($exam->subject_id !== $subject->id || $subject->career_id !== $career->id || $career->university_id !== $university->id) {
            // This check might be redundant if routes are properly structured and bound,
            // but it adds an extra layer of safety.
            // A 404 might be more appropriate than an empty collection depending on desired behavior.
            // For now, let's assume valid route binding.
        }

        // Start with top-level comments for the given exam (where parent_id is null)
        $comment_query = $exam->comments()
            ->whereNull('parent_id')
            ->with([
                'user:id,name,display_name', // Load only id and name for the user
                'attachments', // Load attachments for top-level comments
                // Recursively load replies with their users and attachments
                'replies' => function ($query) {
                    $query->with(['user:id,name', 'attachments', 'replies']) // Add 'replies' here for nested replies if depth > 1 is needed
                        ->orderBy('created_at', 'asc'); // Sort replies by creation date
                }
            ]);

        // Por defecto mostrar solo comentarios de examen, a menos que se especifique resolution=true
        if ($request->boolean('resolution')) {
            $comment_query->where('comment_type', 'resolution');
        } else {
            $comment_query->where('comment_type', 'exam');
        }

        // Sorting parameters
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        // Allowed columns to sort top-level comments
        $allowedSortColumns = ['created_at', 'upvotes', 'downvotes'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at'; // Default to created_at if invalid
        }

        // Validate sort order
        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $sortOrder = 'desc'; // Default to descending if invalid
        }

        // Apply sorting to the top-level comments query
        $comment_query->orderBy($sortBy, $sortOrder);

        // Pagination parameters
        $perPage = $request->query('per_page', 15); // Default per page
        // Ensure per_page is a positive integer, maybe cap it
        $perPage = max(1, (int) $perPage); // Minimum 1
        $perPage = min(100, $perPage); // Example maximum 100, adjust as needed


        // Get the paginated results
        $comments = $comment_query->paginate($perPage);

        // Return the paginated resource collection
        return new CommentResourceCollection($comments);
    }

    /**
     * Store a new comment.
     *
     * @param  \App\Models\University  $university
     * @param  \App\Models\Career  $career
     * @param  \App\Models\Subject  $subject
     * @param  \App\Models\Exam  $exam
     * @param  \App\Http\Requests\StoreCommentRequest  $request
     * @return \App\Http\Resources\CommentResource
     */
    public function store(University $university, Career $career, Subject $subject, Exam $exam, StoreCommentRequest $request): CommentResource
    {
        // Ensure the exam belongs to the correct university, career, and subject
        if ($exam->subject_id !== $subject->id || $subject->career_id !== $career->id || $career->university_id !== $university->id) {
            abort(404, 'Exam not found in the specified subject, career, or university.');
        }

        // Verificar que el examen tenga una resolución si el comentario es de tipo 'resolution'
        if ($request->comment_type === 'resolution' && !$exam->resolution()->exists()) {
            return response()->json(['message' => 'No se pueden agregar comentarios de resolución a un examen que no tiene resolución'], 422);
        }

        // Check if the parent_id is valid and belongs to the same exam if provided
        if ($request->parent_id) {
            $parentComment = Comment::where('exam_id', $exam->id)
                ->find($request->parent_id);
            if (!$parentComment) {
                abort(422, 'The selected parent comment is invalid or does not belong to this exam.');
            }
        }

        $comment = Comment::create([
            'user_id' => Auth::id(), // Ensure user is authenticated
            'exam_id' => $exam->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'comment_type' => $request->comment_type ?? 'exam', // Default to 'exam' if not specified
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            // Assuming attachments are allowed for both top-level and replies
            foreach ($request->file('attachments') as $file) {
                // Basic file validation (size, mime types) should be in the StoreCommentRequest
                $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $fileName = $baseName . '-' . time() . '.' . $file->getClientOriginalExtension();
                $filepath = $file->storePubliclyAs('attachments', $fileName, 'public'); // Store in public disk

                $comment->attachments()->create([
                    'file_path' => $filepath,
                    'original_file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // Load relationships needed for the resource, including nested replies
        $comment->load([
            'user:id,display_name, name', // Load only id and name
            'attachments', // Load attachments for the new comment
            // If the new comment is a reply, load its user and attachments
            'replies' => function ($query) {
                $query->with(['user:id,name', 'attachments', 'replies']) // Add 'replies' if needed
                    ->orderBy('created_at', 'asc');
            }
        ]);


        return new CommentResource($comment);
    }

    /**
     * Update the specified comment.
     *
     * @param  \App\Models\University  $university
     * @param  \App\Models\Career  $career
     * @param  \App\Models\Subject  $subject
     * @param  \App\Models\Exam  $exam
     * @param  \App\Http\Requests\UpdateCommentRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \App\Http\Resources\CommentResource|\Illuminate\Http\JsonResponse
     */
    public function update(University $university, Career $career, Subject $subject, Exam $exam, UpdateCommentRequest $request, Comment $comment): CommentResource | HttpJsonResponse
    {
        // Ensure the comment belongs to the correct exam/subject/career/university
        if ($comment->exam_id !== $exam->id || $exam->subject_id !== $subject->id || $subject->career_id !== $career->id || $career->university_id !== $university->id) {
            abort(404, 'Comment not found in the specified exam, subject, career, or university.');
        }

        if (Auth::id() !== $comment->user_id) {
            return response()->json(['message' => 'No tienes permiso para editar este comentario'], 403);
        }

        $comment->update($request->validated());

        // Handle attachments if they are part of the update process (e.g., adding more)
        // Deleting attachments would require a different mechanism (e.g., specific endpoint or include attachment IDs to delete in the request)
        if ($request->hasFile('attachments')) {
            // Assuming attachments are allowed for updates
            foreach ($request->file('attachments') as $file) {
                $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $fileName = $baseName . '-' . time() . '.' . $file->getClientOriginalExtension();
                $filepath = $file->storePubliclyAs('attachments', $fileName, 'public');

                $comment->attachments()->create([
                    'file_path' => $filepath,
                    'original_file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // Load relationships needed for the resource, including nested replies
        $comment->load([
            'user:id,display_name,name',
            'attachments',
            'replies' => function ($query) {
                $query->with(['user:id,name', 'attachments', 'replies']) // Add 'replies' if needed
                    ->orderBy('created_at', 'asc');
            }
        ]);

        return new CommentResource($comment);
    }

    /**
     * Remove the specified comment.
     *
     * @param  \App\Models\University  $university
     * @param  \App\Models\Career  $career
     * @param  \App\Models\Subject  $subject
     * @param  \App\Models\Exam  $exam
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(University $university, Career $career, Subject $subject, Exam $exam, Comment $comment): HttpJsonResponse
    {
        if ($comment->exam_id !== $exam->id || $exam->subject_id !== $subject->id || $subject->career_id !== $career->id || $career->university_id !== $university->id) {
            abort(404, 'Comment not found in the specified exam, subject, career, or university.');
        }

        if (Auth::id() !== $comment->user_id) {
            return response()->json(['message' => 'No tienes permiso para eliminar este comentario'], 403);
        }

        // Before deleting the comment, handle replies and attachments.
        // If a comment with replies is deleted, its replies will also be deleted due to foreign key constraints (assuming `onDelete('cascade')` is set up in migrations).
        // You might want to explicitly delete replies if cascade isn't used or for specific logic.

        // Eliminar archivos adjuntos de este comentario
        foreach ($comment->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            // You might also need to delete the Attachment model record
            $attachment->delete(); // Assuming you have a method for this or relying on cascade
        }

        $comment->delete(); // This should also cascade delete replies and their attachments if configured in the database schema.

        return response()->json(['message' => 'Comentario eliminado exitosamente']);
    }

    /**
     * Handle voting for a comment.
     *
     * @param  \App\Models\University  $university
     * @param  \App\Models\Career  $career
     * @param  \App\Models\Subject  $subject
     * @param  \App\Models\Exam  $exam
     * @param  \App\Models\Comment  $comment
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function vote(University $university, Career $career, Subject $subject, Exam $exam, Comment $comment, Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Ensure the comment belongs to the correct exam/subject/career/university
        if ($comment->exam_id !== $exam->id || $exam->subject_id !== $subject->id || $subject->career_id !== $career->id || $career->university_id !== $university->id) {
            abort(404, 'Comment not found in the specified exam, subject, career, or university.');
        }


        $voteType = $request->input('vote_type');
        // Allow 'up', 'down', or 'unvote' as valid types from the frontend logic
        $allowedVoteTypes = ['up', 'down', 'unvote'];
        if (!in_array($voteType, $allowedVoteTypes)) {
            return response()->json(['message' => 'Tipo de voto inválido'], 400);
        }

        $userId = Auth::id();
        $existingVote = $comment->votes()->where('user_id', $userId)->first();

        if ($existingVote) {
            if ($voteType === 'unvote' || $existingVote->vote_type === $voteType) {
                // If 'unvote' is requested, or the user is clicking the same vote again
                $existingVote->delete();
                // Decrement the previous vote count
                $comment->decrement($existingVote->vote_type === 'up' ? 'upvotes' : 'downvotes');
            } else {
                // User is changing their vote
                $existingVote->update(['vote_type' => $voteType]);
                // Decrement the old vote and increment the new one
                $comment->decrement($voteType === 'up' ? 'downvotes' : 'upvotes');
                $comment->increment($voteType === 'up' ? 'upvotes' : 'downvotes');
            }
        } elseif ($voteType !== 'unvote') {
            // No existing vote and a new vote ('up' or 'down') is requested
            $comment->votes()->create([
                'user_id' => $userId,
                'vote_type' => $voteType
            ]);
            $comment->increment($voteType === 'up' ? 'upvotes' : 'downvotes');
        }
        // If $voteType is 'unvote' and no existing vote, do nothing.

        // Refresh the comment model to get the updated vote counts
        $comment->refresh();

        // Load relationships needed for the resource, including nested replies
        // Ensure we load the user and attachments for all levels of replies
        $comment->load([
            'user:id,name',
            'attachments',
            'replies' => function ($query) {
                $query->with(['user:id,name', 'attachments', 'replies' => function ($q) {
                    $q->with(['user:id,name', 'attachments'])->orderBy('created_at', 'asc'); // Deeper nesting if needed
                }])
                    ->orderBy('created_at', 'asc'); // Sort replies by creation date
            }
        ]);


        return response()->json(['data' => new CommentResource($comment)], 200); // Return the updated comment using the resource
    }
}
