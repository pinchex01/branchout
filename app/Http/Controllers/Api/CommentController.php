<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'ref_id' => "required",
            "ref_type" => "required",
            "comment" => "required",
            'reply_id' => "null|exists:comments,pk"
        ]);

        if($item  = $this->get_comment_item($request)){
            $notes = $request->input('comment');
            $reply_id = $request->input('reply_id');

            $comment  = Comment::add_comment($item, $notes, [
                'user_id' => $request->user()->id,
                'reply_id' => $reply_id ? : null
            ]);

            return response()->json($comment, 200);
        }

        return response()->json([
            'status' => 'fail',
            'message' => "Seems you are trying to comment on something that does not exist anymore"
        ], 430);
    }

    /**
     * @param Request $request
     * @return null
     */
    private function get_comment_item(Request $request)
    {
        $type = $request->input('ref_type');
        $id = $request->input('ref_id');

        switch ($type){
            case 'event':
                return Event::find($id);
                break;
            default:
                return null;
        }
    }
}
