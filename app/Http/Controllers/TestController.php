<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function testEdit()
    {
        $artwork = Artwork::first();
        $user = Auth::user();

        if (!$artwork) {
            return response()->json(['error' => 'No artwork found']);
        }

        if (!$user) {
            return response()->json(['error' => 'User not authenticated']);
        }

        return response()->json([
            'artwork_id' => $artwork->id,
            'artwork_title' => $artwork->getTitle(),
            'artwork_user_id' => $artwork->user_id,
            'current_user_id' => $user->id,
            'users_match' => $user->id === $artwork->user_id,
            'artwork_status' => $artwork->status,
            'artwork_visibility' => $artwork->visibility,
            'artwork_license' => $artwork->license_type,
        ]);
    }
}
