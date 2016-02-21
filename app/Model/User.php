<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends Model implements UserInterface
{
    public $timestamps = false;

    public function getUsername()
    {
        return $this->login;
    }

    public function getPassword()
    {
        return $this->password;
    }
    
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
    }

    public function eraseCredentials() {}
}
