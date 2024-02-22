<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        $this->can('view-uploads');

        $uploads  = Upload::with(['author'])->paginate(20);

        return view('admin.uploads.index',[
            'uploads' => $uploads
        ])->with('page_title',"Uploads");
    }

    public function show(Upload $upload)
    {
        //$this->can('view-uploads');

        return view('admin.uploads.view',[
            'upload' => $upload
        ])->with('page_title', "Upload #{$upload->id} - {$upload->name} | ");
    }

    public function download(Upload $upload)
    {
        //$this->view('view-uploads');
        $file = storage_path('app/'.$upload->source_url);

        return response()->download($file);
    }

    public function preview(Upload $upload, Request $request)
    {
        $file = storage_path('app/'.$upload->source_url);

        return \Image::make($file)->response();
    }

    public function destroy(Upload $upload)
    {
        $this->can('delete-uploads');
        Upload::remove($upload);

        return redirect()->back()
            ->with([
                'alerts' => [
                    "type" =>"success", "message" => "Upload {$upload} successfully deleted"
                ]
            ]);
    }
}
