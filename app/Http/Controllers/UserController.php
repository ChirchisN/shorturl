<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function user()
    {
        if (Auth::check()) {
            $user = Auth::user();

            return response()->json([
                'firstName' => $user->first_name,
                'lastName' => $user->last_name
            ]);
        } else {
            return response()->json(['message' => "User is not logged!"], 404);
        }
    }
}
