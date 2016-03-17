<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Auth
{
    public function login(Application $app, Request $request)
    {
        return $app['twig']->render('login.html.twig', [
            'login_paths'   => $app['oauth.login_paths'],
            'error'         => $app['security.last_error']($request),
        ]);
    }
}
