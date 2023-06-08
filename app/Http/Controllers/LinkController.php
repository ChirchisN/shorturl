<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LinkController extends Controller
{

    public function create(Request $request)
    {
        if (Auth::check()) {

            $validator = Validator::make($request->all(), [
                'link' => 'required',
                'short_code' => ['max:50', 'unique:links']
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'message'=>'Empty field!'], 400);
            }

            $shortCode = !empty($request['short_code']) ? $request['short_code'] : uniqid();

            Link::create([
                'user_id' => Auth::id(),
                'original_link' => $request['link'],
                'short_code' => $shortCode
            ]);

            return response()->json(['link' => url(route('home')).'/link/'. $shortCode]);
        } else {
            return response()->json(['message' => 'User is not logged'], 400);
        }
    }
}
