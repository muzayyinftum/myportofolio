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
            'type' => 'required|in:jurnal,konferensi,hakki',
            'authors' => 'required|array',
            'authors.*' => 'string',
            'journal_name' => 'nullable|string|max:255',
            'conference_name' => 'nullable|string|max:255',
            'hakki_type' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'volume' => 'nullable|string|max:50',
            'issue' => 'nullable|string|max:50',
            'pages' => 'nullable|string|max:50',
            'doi' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,submitted,accepted,published',
            'description' => 'nullable|string',
            'link' => 'nullable|url|max:500',
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
            'type' => 'sometimes|in:jurnal,konferensi,hakki',
            'authors' => 'sometimes|array',
            'authors.*' => 'string',
            'journal_name' => 'nullable|string|max:255',
            'conference_name' => 'nullable|string|max:255',
            'hakki_type' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'volume' => 'nullable|string|max:50',
            'issue' => 'nullable|string|max:50',
            'pages' => 'nullable|string|max:50',
            'doi' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,submitted,accepted,published',
            'description' => 'nullable|string',
            'link' => 'nullable|url|max:500',
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

