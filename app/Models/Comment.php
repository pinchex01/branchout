<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
        'item_id','item_type','notes','user_id', 'name', 'email', 'reply_id', 'pk'
    ];

    public function item()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param $item
     * @param $notes
     * @param array $props
     * @return Comment|null
     */
    public static function add_comment($item, $notes, array $props)
    {
        $comment = null;

        \DB::transaction(function () use (&$comment, $item, $notes, $props){
            $comment = new self([
                "item_id" => $item->id,
                "item_type" => get_class($item),
                "notes" => $notes,
            ]);
            $comment->fill(map_props_to_params($props, $comment->getFillable()));

            $comment->save();
        });

        return $comment;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($comment){
            $comment->pk = \Uuid::generate()->string;
        });
    }
}
