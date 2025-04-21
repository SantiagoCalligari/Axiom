<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CareerController;
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

// Public access to Universities (Listing and Viewing individual universities)
Route::get('/universities', [UniversityController::class, 'index']);
Route::get('/universities/{university}', [UniversityController::class, 'show']);
Route::get('/universities/{university}/careers', [CareerController::class, 'index']);
Route::get('/universities/{university}/careers/{career}', [CareerController::class, 'show']);
Route::get('/universities/{university}/careers/{career}/subjects', [SubjectController::class, 'index']);
Route::get('/universities/{university}/careers/{career}/subjects/{subject}', [SubjectController::class, 'show']);


// --- Authenticated Routes ---
// These routes require a valid API token
Route::middleware('auth:api')->group(function () {

    // Authenticated User Actions
    // Assuming the original GET /users and POST /users/update were for the authenticated user.
    // Using a dedicated /auth/user route makes this clearer.
    // You will need to add 'showAuthenticatedUser' and 'updateAuthenticatedUser' methods
    // to your UserController that retrieve/update the currently authenticated user.
    Route::get('/auth/user', [UserController::class, 'show']);
    // Using PUT for update following REST conventions
    Route::post('/auth/user', [UserController::class, 'update']);


    // University Management (Authenticated Actions)
    // Public GET actions (index, show) are defined above.
    // These are the protected actions (Create, Update, Delete).
    // Note: Using PUT for update following REST conventions.
    Route::post('/universities', [UniversityController::class, 'store'])->can(Permission::STORE_UNIVERSITY); // Create new university
    Route::post('/universities/{university}', [UniversityController::class, 'update'])->can(Permission::STORE_UNIVERSITY); // Update specific university
    Route::delete('/universities/{university}', [UniversityController::class, 'destroy'])->can(Permission::DELETE_UNIVERSITY); // Delete specific university

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
    Route::post('/universities/{university}/careers', [CareerController::class, 'store'])->can(Permission::STORE_CAREER);
    Route::post('/universities/{university}/careers/{career}', [CareerController::class, 'update'])->can(Permission::STORE_CAREER);
    Route::delete('/universities/{university}/careers/{career}', [CareerController::class, 'destroy'])->can(Permission::DELETE_CAREER);


    // subjects Management (Authenticated Actions)
    Route::post('/universities/{university}/careers/{career}/subjects', [SubjectController::class, 'store']);
    Route::post('/universities/{university}/careers/{career}/subjects/{subject}', [SubjectController::class, 'update']);
    Route::delete('/universities/{university}/careers/{career}/subjects/{subject}', [SubjectController::class, 'destroy']);

    /*
    // If you need other authenticated career actions (listing, showing, updating, deleting)
    // related to a university, you could add them here or use a nested resource:
    Route::get('/universities/{university}/careers', [CareerController::class, 'indexForUniversity']); // List careers for a university
    Route::get('/universities/{university}/careers/{career}', [CareerController::class, 'showForUniversity']); // Show a specific career for a university
    Route::put('/universities/{university}/careers/{career}', [CareerController::class, 'updateForUniversity']); // Update a specific career
    Route::delete('/universities/{university}/careers/{career}', [CareerController::class, 'destroyForUniversity']); // Delete a specific career

    // Or using nested Route::resource (more advanced):
    Route::resource('universities.careers', CareerController::class)->only([
        'store', 'index', 'show', 'update', 'destroy'
    ]);
    */
});
