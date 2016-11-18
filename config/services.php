<?php

use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../views',
    'twig.options' => [
        'cache' => __DIR__ . '/../var/cache/twig',
    ],
]);
$app->register(new Silex\Provider\AssetServiceProvider(), [
    'assets.version' => 'v1'
]);

$app->register(new Silex\Provider\SessionServiceProvider(), [
    'session.storage.save_path' => __DIR__ . '/../var/sessions'
]);

$app->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.role_hierarchy' => [],
    'security.firewalls' => [
        'default' => [
            'anonymous' => true,
            'form' => ['login_path' => '/login', 'check_path' => '/login_check'],
            'logout' => ['logout_path' => '/logout', 'invalidate_session' => true],
            'users' => function () {
                return new App\Security\UserProvider();
            },
        ]
    ]
]);

$app->register(new Silex\Provider\FormServiceProvider());
