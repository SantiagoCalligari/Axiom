<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// --- Public Routes ---
// These routes do not require authentication

// Authentication
Route::post('/auth/token', [AuthController::class, 'store_token']);

// User Registration
// Assuming the original POST /users is for creating a new user account
Route::post('/auth/register', [UserController::class, 'store']);
// If you prefer, you could name this '/auth/register' or similar
Route::middleware('auth:api')->group(function () {
    // Authenticated User Actions
    // Authenticated User Actions
    // Assuming the original GET /users and POST /users/update were for the authenticated user.
    // Using a dedicated /auth/user route makes this clearer.
    // You will need to add 'showAuthenticatedUser' and 'updateAuthenticatedUser' methods
    // to your UserController that retrieve/update the currently authenticated user.
    Route::get('/auth/user', [UserController::class, 'show']);
    // Using PUT for update following REST conventions
    Route::post('/auth/user', [UserController::class, 'update']);
});

// Public access to Universities (Listing and Viewing individual universities)
Route::get('/universities', [UniversityController::class, 'index']);
Route::get('/{university}', [UniversityController::class, 'show']);
Route::get('/{university}/careers', [CareerController::class, 'index']);
Route::get('/{university}/{career}', [CareerController::class, 'show']);
Route::get('/{university}/{career}/subjects', [SubjectController::class, 'index']);
Route::get('/{university}/{career}/{subject}', [SubjectController::class, 'show']);
Route::get('/{university}/{career}/{subject}/exams', [ExamController::class, 'index']);
Route::get('/{university}/{career}/{subject}/{exam}', [ExamController::class, 'show']);


// --- Authenticated Routes ---
// These routes require a valid API token
Route::middleware('auth:api')->group(function () {



    // University Management (Authenticated Actions)
    // Public GET actions (index, show) are defined above.
    // These are the protected actions (Create, Update, Delete).
    // Note: Using PUT for update following REST conventions.
    Route::post('/universities', [UniversityController::class, 'store'])->can(Permission::STORE_UNIVERSITY); // Create new university
    Route::post('/{university}', [UniversityController::class, 'update'])->can(Permission::STORE_UNIVERSITY); // Update specific university
    Route::delete('/{university}', [UniversityController::class, 'destroy'])->can(Permission::DELETE_UNIVERSITY); // Delete specific university

    /*
    // Alternative using Route::resource for authenticated university actions:
    Route::resource('universities', UniversityController::class)->only([
        'store', 'update', 'destroy'
    ]);
    // The public 'index' and 'show' would still be defined outside this group.
    */


    // Career Management (Authenticated Actions)
    // Assuming careers are nested under universities.
    // Creating a career for a specific university.
    Route::post('/{university}/careers', [CareerController::class, 'store'])->can(Permission::STORE_CAREER);
    Route::post('/{university}/{career}', [CareerController::class, 'update'])->can(Permission::STORE_CAREER);
    Route::delete('/{university}/{career}', [CareerController::class, 'destroy'])->can(Permission::DELETE_CAREER);


    // subjects Management (Authenticated Actions)
    Route::post('/{university}/{career}/subjects', [SubjectController::class, 'store']);
    Route::post('/{university}/{career}/{subject}', [SubjectController::class, 'update']);
    Route::delete('/{university}/{career}/{subject}', [SubjectController::class, 'destroy']);

    Route::post('/{university}/{career}/{subject}/exams', [ExamController::class, 'store']);
    Route::post('/{university}/{career}/{subject}/{exam}', [ExamController::class, 'update']);
    Route::delete('/{university}/{career}/{subject}/{exam}', [ExamController::class, 'destroy']);
});
