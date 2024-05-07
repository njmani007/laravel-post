<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of all posts.
     */
    public function index()
{
    $userId = Auth::id();
    $posts = Post::where('user_id', $userId)->get();

    return view('post.index', compact('posts'));
}

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        return view('post.create');
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Post::create([
            'user_id' => Auth::id(),
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
        ]);

        return redirect()->route('post.index')->with('success', 'Post created successfully');
    }

    /**
     * Display a specific post.
     */
    public function show($id)
    {
        $post = Post::findOrFail($id); // Find the post or throw a 404 error
        return view('post.show', compact('post')); // Return the view for displaying a post
    }

    /**
     * Show the form for editing an existing post.
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('post.edit', compact('post'));
    }

    /**
     * Update an existing post in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $post = Post::findOrFail($id);
        $post->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
        ]);

        return redirect()->route('post.index')->with('success', 'Post updated successfully');
    }

    /**
     * Delete a specific post from storage.
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('post.index')->with('success', 'Post deleted successfully');
    }
}
