<?php
/**
 * Created by PhpStorm.
 * User: mitac
 * Date: 3/23/2017
 * Time: 1:33 PM
 */

namespace App;


use App\Models\Upload;
use Illuminate\Http\UploadedFile;

trait UploadsTrait
{
    /**
     * Get all related uploads
     *
     * @return mixed
     */
    public function uploads()
    {
        return $this->morphMany(Upload::class, "item", "item_type", "item_id");
    }

    /**
     * Get name of how the file should be stored if not, use the file name
     *
     * @return string|null
     */
    public function getUploadFileName()
    {
        $upload_file_name  = $this->{$this->upload_file_name_source} ? : $this->name;

        return $upload_file_name ? : null;
    }

    /**
     * Get upload destination from model store else use custom
     *
     * @return string
     */
    public function getUploadDirectory()
    {
        return $this->upload_directory ? : 'uploads';
    }

    /**
     * Upload file
     *
     * @param UploadedFile $file
     *
     * @param null $name
     * @param User $user
     * @return Upload|null
     */
    public function upload(UploadedFile $file = null, $name = null, User $user)
    {
        if (!$file) return;

        $name = $name ?  : $this->getUploadFileName();
        return Upload::new_upload($this, $file, $name, $user);
    }
}