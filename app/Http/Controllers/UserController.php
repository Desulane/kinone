<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function settings()
    {
        return view('settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'image_quality' => 'required|in:default,preview',
        ]);

        Auth::user()->update(['image_quality' => $request->image_quality]);

        return redirect()->route('settings')->with('success', 'Настройки сохранены.');
    }
}
