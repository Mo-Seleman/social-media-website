<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class FriendController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $user = $request->user()->load('following');

        return Inertia::render('MyFriends', [
            'following' => UserResource::collection($user->following),
        ]); 
    }
}
