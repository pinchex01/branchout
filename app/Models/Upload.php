<?php

namespace App\Models;

use App\SlugableTrait;
use App\UploadsTrait;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\Activitylog\Traits\LogsActivity;
use Venturecraft\Revisionable\RevisionableTrait;

class Upload extends Model
{
    use SlugableTrait, RevisionableTrait, LogsActivity;

    protected $table = 'uploads';

    protected $fillable = [
        'item_id','item_type', 'name', 'slug', 'thumbnail_url','source_url', 'type', 'size', 'user_id'
    ];

    protected $keepRevisionOf = [
        'item_id','item_type', 'name', 'slug', 'thumbnail_url','source_url','type'
    ];

    protected $logAttributes = [
        'item_id','item_type', 'name', 'slug', 'thumbnail_url','source_url', 'type'
    ];

    /**
     * Column used to generate a slug
     *
     * @var string
     */
    private $slug_source = 'name';

    public function item()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Store file and create upload entry
     *
     * @param $item
     *
     * @param UploadedFile $file
     * @param null $name
     * @param User $user
     * @return Upload|null
     */
    public static function new_upload($item,  UploadedFile $file, $name = null, User $user)
    {
        $upload  = null;
        \DB::transaction(function () use (&$file, $item, &$upload, $name, $user){

            //get name or use file original_name
            $name  = $name ? : $file->getClientOriginalName();

            //store file and get path
            $time_hash  = md5(time());
            $path = $file->storeAs($item->getUploadDirectory(), str_slug($name)."-{$time_hash}.".$file->extension());

            //save upload entry
            $upload = new self([
                "item_id" => $item->id,
                "item_type" => get_class($item),
                "name" => $name,
                "source_url" => $path,
                "type" => $file->extension(),
                'size' => $file->getSize(),
                'user_id' => $user? $user->id : null
            ]);
            $upload->save();

            //log activity
            activity('feed')->log("$user uploaded a file {$upload} ({$upload->type})")
                ->causedBy($user);
        });
    }

    /**
     *  Delete file and remove upload entry
     *
     * @param Upload $upload
     */
    public static function remove(Upload $upload)
    {
        //delete file
        \DB::transaction(function () use (&$upload){
            //first delete file
            \Storage::delete($upload->source_url);

            //delete entry
            $upload->delete();

        });

    }

}
