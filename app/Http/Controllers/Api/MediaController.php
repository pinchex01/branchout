<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    public function uploadAvatar(Request $request)
    {
        $this->validate($request, [
            'file' => "required|image|mimes:png,jpg,jpeg,gif"
        ]);

        $file = $request->file('file');

        //store file and get path
        $path = $file->storeAs('avatars', md5(time()));

        return response()->json([
            'path' => $path
        ], 200);

    }
    
    public function upload(Request $request)
    {
        $this->validate($request, [
            'file' => "required|file|mimes:png,jpeg,jpg,pdf,docx,doc,xlxs,ppt,bmp",
            'type' => "required|in:temp"
        ]);

        $file = $request->file('file');

        //store file and get path
        $path = $file->storeAs('temp', md5(time()));

        return response()->json([
            'path' => $path
        ], 200);
    }

    public function viewImage(Request $request, $path)
    {
        $path = base64_decode($path);
        $file = storage_path("app/{$path}");

        return \Image::make($file)->response();
    }
}
