<?php

Route::get('/debug-admin', function () {
    try {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json([
                'authenticated' => true,
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'name' => $user->name
            ]);
        } else {
            return response()->json(['authenticated' => false]);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
