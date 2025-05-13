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
    public function index(University $university, Career $career, Subject $subject, Exam $exam, Request $request): CommentResourceCollection
    {
        $comment_query = $exam->comments()->with(['user', 'replies.user', 'attachments']);

        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        $allowedSortColumns = ['created_at', 'upvotes', 'downvotes'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $comment_query->orderBy($sortBy, $sortOrder);

        $perPage = $request->query('per_page', 15);
        $comments = $comment_query->paginate($perPage);

        return new CommentResourceCollection($comments);
    }

    public function store(University $university, Career $career, Subject $subject, Exam $exam, StoreCommentRequest $request): CommentResource
    {
        $comment = Comment::create([
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        if ($request->hasFile('attachments')) {
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

        $comment->load(['user', 'attachments']);
        return new CommentResource($comment);
    }

    public function update(University $university, Career $career, Subject $subject, Exam $exam, UpdateCommentRequest $request, Comment $comment): CommentResource | JsonResponse
    {
        if (Auth::id() !== $comment->user_id) {
            return response()->json(['message' => 'No tienes permiso para editar este comentario'], 403);
        }

        $comment->update($request->validated());

        if ($request->hasFile('attachments')) {
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

        $comment->load(['user', 'attachments']);
        return new CommentResource($comment);
    }

    public function destroy(University $university, Career $career, Subject $subject, Exam $exam, Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            return response()->json(['message' => 'No tienes permiso para eliminar este comentario'], 403);
        }

        // Eliminar archivos adjuntos
        foreach ($comment->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }

        $comment->delete();
        return response()->json(['message' => 'Comentario eliminado exitosamente']);
    }

    public function vote(University $university, Career $career, Subject $subject, Exam $exam, Comment $comment, Request $request)
    {
        $voteType = $request->input('vote_type');
        if (!in_array($voteType, ['up', 'down'])) {
            return response()->json(['message' => 'Tipo de voto inválido'], 400);
        }

        $userId = Auth::id();
        $existingVote = $comment->votes()->where('user_id', $userId)->first();

        if ($existingVote) {
            if ($existingVote->vote_type === $voteType) {
                // Si el voto es del mismo tipo, lo eliminamos
                $existingVote->delete();
                $comment->decrement($voteType === 'up' ? 'upvotes' : 'downvotes');
            } else {
                // Si el voto es de tipo diferente, lo actualizamos
                $existingVote->update(['vote_type' => $voteType]);
                $comment->increment($voteType === 'up' ? 'upvotes' : 'downvotes');
                $comment->decrement($voteType === 'up' ? 'downvotes' : 'upvotes');
            }
        } else {
            // Crear nuevo voto
            $comment->votes()->create([
                'user_id' => $userId,
                'vote_type' => $voteType
            ]);
            $comment->increment($voteType === 'up' ? 'upvotes' : 'downvotes');
        }

        $comment->load(['user', 'attachments']);
        return new CommentResource($comment);
    }
}

