<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends Model implements UserInterface
{
    public function oauth_users()
    {
        return $this->hasMany('App\\Model\\OAuthUser');
    }

    public function getUsername()
    {
        return $this->id;
    }

    public function getPassword() {}

    public function getRoles()
    {
        return $this->is_admin ? ['ROLE_ADMIN'] : ['ROLE_USER'];
    }

    public function getSalt() {}

    public function eraseCredentials() {}
}
