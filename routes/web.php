<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/posts', function () { return view('vue'); });
Route::get('/portal_nilai', function () { return view('portal_nilai'); });
Route::get('/admin', function () { return view('admin'); });

// Admin route untuk melihat data posts
Route::get('/admin/posts', function () {
    $posts = \App\Models\Post::orderBy('created_at', 'desc')->get();
    return view('admin.posts', compact('posts'));
});

// Catch-all route untuk handle reload pada SPA
// Pastikan route ini di akhir untuk tidak mengganggu route lainnya
Route::fallback(function () {
    // Jika request ke route yang tidak ada, kembalikan 404
    // Atau redirect ke home jika ingin
    abort(404);
});
