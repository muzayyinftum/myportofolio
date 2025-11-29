<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Posts Routes
Route::apiResource('posts', \App\Http\Controllers\PostController::class);

// Portal Nilai - login dengan NIM + password
Route::post('/portal/login', [\App\Http\Controllers\StudentPortalController::class, 'login']);

// Admin Portal - login admin
Route::post('/admin/login', [\App\Http\Controllers\StudentPortalController::class, 'loginAdmin']);

// Admin Portal - get all students (perlu header X-Admin-Auth)
Route::get('/admin/students', [\App\Http\Controllers\StudentPortalController::class, 'getAllStudents']);

// Admin Portal - create mahasiswa baru
Route::post('/admin/students', [\App\Http\Controllers\StudentPortalController::class, 'createStudent']);

// Admin Portal - update nilai mahasiswa
Route::put('/admin/students/{id}', [\App\Http\Controllers\StudentPortalController::class, 'updateNilai']);
