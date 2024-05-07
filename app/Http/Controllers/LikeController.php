<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{


    public function likePost($postId)
    {
        $user = Auth::user();

        $like = Like::firstOrCreate([
            'user_id' => $user->id,
            'post_id' => $postId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post liked successfully!',
            'like_id' => $like->id,
        ]);
    }

    public function unlikePost($postId)
    {
        $user = Auth::user();

        $like = Like::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->first();

        if ($like) {
            $like->delete();
            return response()->json([
                'success' => true,
                'message' => 'Post unliked successfully!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Like not found.',
        ]);
    }

    public function countLikes($postId)
    {
        $post = Post::findOrFail($postId);

        $likeCount = $post->likes()->count();

        return response()->json([
            'success' => true,
            'like_count' => $likeCount,
        ]);
    }
}
