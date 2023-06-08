<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
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
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Empty field or link already exists!'
                ], 400);
            }

            $shortCode = !empty($request['short_code']) ? $request['short_code'] : uniqid();

            Link::create([
                'user_id' => Auth::id(),
                'original_link' => $request['link'],
                'short_code' => $shortCode
            ]);

            return response()->json(['link' => url(route('home')) . '/lk/' . $shortCode]);
        } else {
            return response()->json(['message' => 'User is not logged'], 400);
        }
    }

    public function redirect($shortCode)
    {
        $link = Link::where('short_code', $shortCode)->first();

        if (!empty($link)) {
            $link->redirected_count += 1;
            $link->save();
            return redirect($link->original_link);
        }

        abort(404);
    }

    public function getLinks()
    {
        if (Auth::check()) {
            $userRole = Auth::getUser()->role;
            $userId = Auth::getUser()->id;

            if ($userRole == 'USER') {
                $links = Link::where(['user_id' => $userId])->get()->sortByDesc("id");

                return response()->json($links);
            } elseif ($userRole == 'ADMIN') {
                $links = Link::all()->sortByDesc("id");

                return response()->json($links);
            }
        }

        return response()->json();
    }
}
