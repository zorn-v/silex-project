<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OAuthUser extends Model
{
    protected $table = 'oauth_users';

    public function user()
    {
        return $this->belongsTo('App\\Model\\User');
    }
}
