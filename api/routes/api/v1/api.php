<?php

use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\CommentController;
use App\Http\Controllers\Api\v1\HomeController;
use App\Http\Controllers\Api\v1\PostController;
use App\Http\Controllers\Api\v1\RelationController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/', HomeController::class);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/password/reset', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset/confirm', [AuthController::class, 'reset']);
    Route::get('/users', [UserController::class, 'getUsers']);
    Route::post('/users', [UserController::class, 'createUser']);
    Route::get('/posts', [PostController::class, 'getPosts']);
    Route::get('/post/{id}', [PostController::class, 'getPost']);
    Route::put('/post/{id}', [PostController::class, 'updatePost']);
    Route::delete('/post/{id}', [PostController::class, 'deletePost']);
    Route::post('/posts/{postId}/comments', [CommentController::class, 'createComment']);
    Route::delete('/comments/{id}', [CommentController::class, 'deleteComment']);

});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/post/{id}/like', [PostController::class, 'likePost']);
    Route::get('/post/{id}/unlike', [PostController::class, 'unlikePost']);
    Route::get('/post/{id}/share', [PostController::class, 'sharePost']);
    Route::get('/users/{id}/posts', [PostController::class, 'getUserPosts']);
    Route::post('/posts', [PostController::class, 'createPost']);

    Route::get('/users/{id}', [UserController::class, 'getUser']);
    Route::put('/users/{id}', [UserController::class, 'updateUser']);
    Route::delete('/users/{id}', [UserController::class, 'deleteUser']);

    Route::post('/friends/request', [RelationController::class, 'sendFriendRequest']);
    Route::post('/friends/accept/{friendshipId}', [RelationController::class, 'acceptFriendRequest']);
    Route::post('/friends/reject/{friendshipId}', [RelationController::class, 'rejectFriendRequest']);
    Route::get('/friends', [RelationController::class, 'getFriends']);
    Route::get('/friends/pending', [RelationController::class, 'getPendingRequests']);
    Route::get('/friends/sending', [RelationController::class, 'getSentRequests']);
    Route::delete('/friends/{friendId}', [RelationController::class, 'unfriend']);
    Route::post('/users/{userId}/block', [RelationController::class, 'blockUser']);
    Route::delete('/users/{userId}/unblock', [RelationController::class, 'unblockUser']);

    Route::get("/admin/users", [AdminController::class, 'getAdminUsers']);
    Route::get("/admin/posts", [AdminController::class, 'getAdminPosts']);
    Route::get('/admin/post/{id}', [PostController::class, 'getPost']);
    Route::put('/admin/post/{id}', [PostController::class, 'updatePost']);
    Route::delete('/admin/post/{id}', [PostController::class, 'deletePost']);

    Route::get("/admin/stats", [AdminController::class, 'getAdminStats']);
});

Route::middleware('auth:sanctum')->post('resend/email', [UserController::class, 'resendEmailActivation']);
Route::get('/activate-account/{token}', [UserController::class, 'activateAccount']);

