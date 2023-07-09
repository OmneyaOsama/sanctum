<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;

class StatsController extends Controller
{
    public function getStats()
    {
        $userCount = User::count();
        $postCount = Post::count();
        $usersWithZeroPostsCount = User::whereDoesntHave('posts')->count();

        return response()->json([
            'userCount' => $userCount,
            'postCount' => $postCount,
            'usersWithZeroPostsCount' => $usersWithZeroPostsCount,
        ]);
    }
}
