<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $file = $request->file('image');
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        
        $path = $file->storeAs('questions', $filename, 'public');

        return response()->json([
            'url' => asset('storage/' . $path)
        ]);
    }
}
