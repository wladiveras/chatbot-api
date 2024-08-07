<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi,mp3,wav',
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads', 's3');

        return response()->json([
            'success' => true,
            'path' => Storage::disk('s3')->url($path),
        ]);
    }
}
