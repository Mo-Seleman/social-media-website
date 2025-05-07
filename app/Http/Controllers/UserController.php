<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follower;
use Illuminate\Http\Request;
use App\Notifications\NewFollower;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    public function follow(Request $request, User $user)
    {

        $data = $request->validate([
            'follow' => ['boolean']
        ]);

        if ($data['follow']) {

            Follower::create([
                'user_id' => $user->id,
                'follower_id' => Auth::id()
            ]);
        } else {

            Follower::query()
                ->where('user_id', $user->id)
                ->where('follower_id', Auth::id())
                ->delete();
        };

        $user->notify(new NewFollower(Auth::user(), $data['follow']));

        return back();
    }
}
