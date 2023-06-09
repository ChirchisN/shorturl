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
                'link' => ['required', 'url'],
                'short_code' => ['max:50', 'unique:links']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Empty field, link already exists or URL is not valid!'
                ], 400);
            }

            $shortCode = !empty($request['short_code']) ? $request['short_code'] : uniqid();

            $link = Link::create([
                'user_id' => Auth::id(),
                'original_link' => $request['link'],
                'short_code' => $shortCode
            ]);

            $adjustedLink = [
                'id' => $link['id'],
                'original_link' => $link['original_link'],
                'short_link' => url(route('home')) . '/lk/' . $link['short_code'],
                'redirected_count' => $link['redirected_count']
            ];

            return response()->json($adjustedLink);
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
                $links = Link::where(['user_id' => $userId])->orderBy('id', 'desc')->get();
                $adjustedLinks = $this->adjustLinks($links);

                return response()->json($adjustedLinks);
            } elseif ($userRole == 'ADMIN') {
                $links = Link::orderBy('id', 'desc')->get();
                $adjustedLinks = $this->adjustLinks($links);

                return response()->json($adjustedLinks);
            }
        }

        return response()->json([]);
    }

    private function adjustLinks($links)
    {
        $adjustedLinks = [];
        foreach ($links as $link) {
            $adjustedLinks[] = [
                'id' => $link['id'],
                'original_link' => $link['original_link'],
                'short_link' => url(route('home')) . '/lk/' . $link['short_code'],
                'redirected_count' => $link['redirected_count']
            ];
        }

        return $adjustedLinks;
    }
}
