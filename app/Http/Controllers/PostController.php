<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Menampilkan semua posts
     */
    public function index()
    {
        $posts = Post::all();
        return response()->json($posts);
    }

    /**
     * Menyimpan post baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'tags' => 'nullable|array',
        ]);

        $post = Post::create($validated);
        return response()->json($post, 201);
    }

    /**
     * Menampilkan post berdasarkan ID
     */
    public function show($id)
    {
        $post = Post::find($id);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post);
    }

    /**
     * Update post
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'author' => 'sometimes|string|max:255',
            'tags' => 'nullable|array',
        ]);

        $post->update($validated);
        return response()->json($post);
    }

    /**
     * Hapus post
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->delete();
        return response()->json(['message' => 'Post deleted successfully']);
    }
}

