<?php

use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
    'twig.form.templates' => ['share/form_layout.html.twig'],
    'twig.options' => [
        'cache' => __DIR__ . '/../cache/twig',
    ],
));
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return $app['request']->getBasePath().'/'.ltrim($asset, '/');
    }));
    return $twig;
}));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.role_hierarchy' => [],
    'security.firewalls' => [
        'default' => [
            'anonymous' => true,
            'form' => ['login_path' => '/login', 'check_path' => '/login_check'],
            'logout' => ['logout_path' => '/logout', 'invalidate_session' => true],
            'users' => $app->share(function () {
                return new App\Security\UserProvider();
            }),
        ]
    ]
));

$app->register(new Silex\Provider\FormServiceProvider());
