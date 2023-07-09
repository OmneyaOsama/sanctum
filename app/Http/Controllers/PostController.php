<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PostController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $posts = $user->posts;

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        /*
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required|string',
            'cover_image' => 'required|image',
            'pinned' => 'required|boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ]);

        $user = Auth::user();

        $post = new Post();
        $post->title = $validatedData['title'];
        $post->body = $validatedData['body'];
        $post->cover_image = $validatedData['cover_image'];
        $post->pinned = $validatedData['pinned'];

        $user->posts()->save($post);

        if (!empty($validatedData['tags'])) {
            $post->tags()->attach($validatedData['tags']);
        }

        return response()->json($post);
        */
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'cover_image' => 'required|image',
                'pinned' => 'required|boolean',
                'tags' => 'required|array',
                'tags.*' => 'exists:tags,id',
            ]);

            DB::beginTransaction();

            $post = new Post();
            $post->title = $validatedData['title'];
            $post->body = $validatedData['body'];
            $post->cover_image = $validatedData['cover_image'];
            $post->pinned = $validatedData['pinned'];
            $post->user_id = auth()->user()->id;

            $post->save();

            $post->tags()->attach($validatedData['tags']);

            DB::commit();

            return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create post', 'error' => $e->getMessage()], 500);
        }
    }




    public function show(Post $post)
    {
        $user = Auth::user();

        if ($user->id !== $post->user_id) {
            return response()->json('Unauthorized', 401);
        }

        return response()->json($post);
    }

    public function update(Request $request, Post $post)
    {
       /* $validatedData = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required|string',
            'cover_image' => 'image',
            'pinned' => 'required|boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ]);

        $user = Auth::user();

        if ($user->id !== $post->user_id) {
            return response()->json('Unauthorized', 401);
        }

        $post->title = $validatedData['title'];
        $post->body = $validatedData['body'];
        $post->pinned = $validatedData['pinned'];

        if ($request->hasFile('cover_image')) {
            $post->cover_image = $validatedData['cover_image'];
        }

        $post->save();

        if (!empty($validatedData['tags'])) {
            $post->tags()->sync($validatedData['tags']);
        }

        return response()->json($post);
        */


       // dd($request->all());
        try {
            // Check if the authenticated user is the owner of the post or has authorization
            if ($post->user_id !== auth()->user()->id) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'cover_image' => 'image',
                'pinned' => 'required|boolean',
                'tags' => 'required|array',
                'tags.*' => 'exists:tags,id',
            ]);

            DB::beginTransaction();

            $post->title = $validatedData['title'];
            $post->body = $validatedData['body'];
            $post->cover_image = $validatedData['cover_image'];
            $post->pinned = $validatedData['pinned'];

            $post->save();

            $post->tags()->sync($validatedData['tags']);

            DB::commit();

            return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update post', 'error' => $e->getMessage()], 500);
        }


    }






    public function destroy(Post $post)
    {
       /* $user = Auth::user();

        if ($user->id !== $post->user_id) {
            return response()->json('Unauthorized', 401);
        }

        $post->delete();

        return response()->json('Post deleted successfully');
        */

            $post = Post::find($id);

            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }

            $post->delete();

            return response()->json(['message' => 'Post deleted successfully']);
        
    }

    public function deletedPosts()
    {
        $user = Auth::user();
        $deletedPosts = $user->posts()->onlyTrashed()->get();

        return response()->json($deletedPosts);
    }

    public function restorePost(Post $post)
    {
        $user = Auth::user();

        if ($user->id !== $post->user_id) {
            return response()->json('Unauthorized', 401);
        }

        $post->restore();

        return response()->json('Post restored successfully');
    }
/*
    public function getStats()
    {
        $totalUsers = User::count();
        $totalPosts = Post::count();
        $usersWithZeroPosts = User::whereDoesntHave('posts')->count();

        return response()->json([
            'total_users' => $totalUsers,
            'total_posts' => $totalPosts,
            'users_with_zero_posts' => $usersWithZeroPosts,
        ]);
    }
    */
}
