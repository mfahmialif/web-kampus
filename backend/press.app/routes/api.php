<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KepalaPenulisPortalController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\NewsCommentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PenulisPortalController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/auth/check-username', [AuthController::class, 'checkUsername']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-email-code', [AuthController::class, 'verifyEmailCode']);
Route::get('/auth/google/config', [AuthController::class, 'googleConfig']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']);

Route::get('/news', [NewsController::class, 'index']);
Route::get('/news-categories', [NewsController::class, 'categories']);
Route::get('/news/{news}', [NewsController::class, 'show']);
Route::get('/public/pages/{slug}', [PageController::class, 'publicShow']);
Route::get('/navigation/primary', [MenuController::class, 'primary']);
Route::get('/settings/general', [SettingController::class, 'general']);

Route::get('/news/{news}/comments', [NewsCommentController::class, 'index']);
Route::post('/news/{news}/comments', [NewsCommentController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/comments/{comment}/like', [NewsCommentController::class, 'like']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('penulis')->group(function () {
        Route::get('/dashboard', [PenulisPortalController::class, 'dashboard']);
        Route::get('/news', [PenulisPortalController::class, 'index']);
        Route::post('/news', [PenulisPortalController::class, 'store']);
        Route::get('/news/{news}', [PenulisPortalController::class, 'show']);
        Route::post('/news/{news}', [PenulisPortalController::class, 'update']);
        Route::delete('/news/{news}', [PenulisPortalController::class, 'destroy']);
        Route::get('/profile', [PenulisPortalController::class, 'profile']);
    });

    Route::prefix('kepala-penulis')->group(function () {
        Route::get('/dashboard', [KepalaPenulisPortalController::class, 'dashboard']);
        Route::get('/news', [KepalaPenulisPortalController::class, 'index']);
        Route::post('/news', [KepalaPenulisPortalController::class, 'store']);
        Route::get('/news/{news}', [KepalaPenulisPortalController::class, 'show']);
        Route::post('/news/{news}', [KepalaPenulisPortalController::class, 'update']);
        Route::delete('/news/{news}', [KepalaPenulisPortalController::class, 'destroy']);
        Route::get('/writers/options', [KepalaPenulisPortalController::class, 'writerOptions']);
        Route::get('/writers', [KepalaPenulisPortalController::class, 'writers']);
        Route::post('/writers', [KepalaPenulisPortalController::class, 'storeWriter']);
        Route::put('/writers/{user}', [KepalaPenulisPortalController::class, 'updateWriter']);
        Route::delete('/writers/{user}', [KepalaPenulisPortalController::class, 'destroyWriter']);
        Route::get('/profile', [KepalaPenulisPortalController::class, 'profile']);
    });

    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('pages', PageController::class);
    Route::get('/menus/primary/items', [MenuController::class, 'items']);
    Route::post('/menus/primary/items', [MenuController::class, 'storeItem']);
    Route::put('/menus/primary/items/{menuItem}', [MenuController::class, 'updateItem']);
    Route::delete('/menus/primary/items/{menuItem}', [MenuController::class, 'destroyItem']);
    Route::post('/menus/primary/reorder', [MenuController::class, 'reorder']);
    Route::get('/menus/primary/options', [MenuController::class, 'options']);
    Route::post('/settings/general', [SettingController::class, 'updateGeneral']);
    Route::get('/settings/email', [SettingController::class, 'email']);
    Route::put('/settings/email', [SettingController::class, 'updateEmail']);
    Route::get('/settings/google-login', [SettingController::class, 'googleLogin']);
    Route::put('/settings/google-login', [SettingController::class, 'updateGoogleLogin']);

    Route::post('/news', [NewsController::class, 'store']);
    Route::put('/news/{news}', [NewsController::class, 'update']);
    Route::delete('/news/{news}', [NewsController::class, 'destroy']);
    Route::get('/news-writers', [NewsController::class, 'writers']);
    Route::apiResource('admin-news-categories', NewsCategoryController::class)
        ->parameters(['admin-news-categories' => 'newsCategory']);

    Route::post('/upload-content-file', [NewsController::class, 'uploadContentFile']);
    Route::post('/delete-content-file', [NewsController::class, 'deleteContentFile']);
});
