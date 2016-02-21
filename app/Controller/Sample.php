<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Sample
{
    public function index(Application $app, Request $request)
    {
        return $app['twig']->render('index.html.twig');
    }
}
