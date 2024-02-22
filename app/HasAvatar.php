<?php
/**
 * Created by PhpStorm.
 * User: mitac
 * Date: 5/6/2017
 * Time: 1:51 PM
 */

namespace App;


trait HasAvatar
{
    public function getAvatar()
    {
        $field =  property_exists($this, 'avatar_field') ? $this->avatar_field: 'avatar';
        return $this->$field ? route('uploads.view',base64_encode( $this->$field)): asset('img/generic-avatar.png');
    }

    public function getFullAvatarPathAttribute()
    {
        $field =  property_exists($this, 'avatar_field') ? $this->avatar_field: 'avatar';

        return storage_path('app/'.$this->$field);
    }
}