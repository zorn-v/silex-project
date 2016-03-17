<?php

namespace App\Security;

use Gigablah\Silex\OAuth\Security\Authentication\Token\OAuthTokenInterface;
use Gigablah\Silex\OAuth\Security\User\Provider\OAuthUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Model\User;
use App\Model\OAuthUser;

class OAuthUserProvider implements UserProviderInterface, OAuthUserProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        return User::find($username);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthCredentials(OAuthTokenInterface $token)
    {
        $oauthUser = OAuthUser::where('service', $token->getService())->where('uid', $token->getUid())->first();
        if ($oauthUser === null) {
            $oauthUser = new OAuthUser();
            $oauthUser->service = $token->getService();
            $oauthUser->uid = $token->getUid();
            $oauthUser->name = $token->getUser();
            $oauthUser->email = $token->getEmail();
            if ($token->isAuthenticated()) {
                $oauthUser->user_id = $token->getAttribute('user_id');
            } else {
                $user = new User();
                $user->name = $token->getUser();
                $user->email = $token->getEmail();
                $user->save();
                $oauthUser->user_id = $user->id;
            }
        } else {
            $oauthUser->name = $token->getUser();
            $oauthUser->email = $token->getEmail();
        }
        $oauthUser->save();
        $token->setUser($oauthUser->user);
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'App\\Model\\User';
    }
}
