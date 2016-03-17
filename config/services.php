<?php

use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../views',
    'twig.form.templates' => ['share/form_layout.html.twig'],
    'twig.options' => [
        'cache' => __DIR__ . '/../var/cache/twig',
    ],
]);
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return $app['request']->getBasePath().'/'.ltrim($asset, '/');
    }));
    return $twig;
}));

$app->register(new Silex\Provider\SessionServiceProvider(), [
    'session.storage.save_path' => __DIR__ . '/../var/sessions'
]);

$app->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.role_hierarchy' => [],
    'security.firewalls' => [
        'default' => [
            'anonymous' => true,
            'oauth' => [
                //'login_path' => '/auth/{service}',
                //'callback_path' => '/auth/{service}/callback',
                //'check_path' => '/auth/{service}/check',
                'failure_path' => '/login',
                'with_csrf' => true
            ],
            'logout' => [
                'logout_path' => '/logout',
                'invalidate_session' => true,
            ],
            'users' => $app->share(function () {
                return new App\Security\OAuthUserProvider();
            }),
        ]
    ],
    'security.access_rules' => [
        ['^/auth', 'ROLE_USER']
    ]
]);

$app->register(new Silex\Provider\FormServiceProvider());

$oauthUserCallback = function ($token, $userInfo, $service) use ($app) {
    if ($app['user'] !== null) {
        $token->setAuthenticated(true);
        $token->setAttribute('user_id', $app['user']->id);
    }
    $uid = isset($userInfo['uid']) ? $userInfo['uid'] : $userInfo['id'];
    $name = isset($userInfo['name']) ? $userInfo['name'] : $userInfo['first_name'].' '.$userInfo['last_name'];
    $email = isset($userInfo['email']) ? $userInfo['email'] : null;

    $token->setUid($uid);
    $token->setUser($name);
    $token->setEmail($email);
};
$app->register(new Gigablah\Silex\OAuth\OAuthServiceProvider(), [
    'oauth.services' => [
        'Facebook' => [
            'key' => 'APP_ID',
            'secret' => 'SECRET_KEY',
            'scope' => ['email'],
            'user_endpoint' => 'https://graph.facebook.com/me?fields=name,email',
            'user_callback' => $oauthUserCallback
        ],
        'Vkontakte' => [
            'key' => 'APP_ID',
            'secret' => 'SECRET_KEY',
            'scope' => ['email'],
            'user_endpoint' => 'https://api.vk.com/method/users.get',
            'user_callback' => function ($token, $userInfo, $service) use ($oauthUserCallback) {
                $userInfo['response'][0]['email'] = $token->getAccessToken()->getExtraParams()['email'];
                $oauthUserCallback($token, $userInfo['response'][0], $service);
            }
        ],
        'Odnoklassniki' => [
            'class' => 'App\\OAuthService\\Odnoklassniki',
            'key' => 'APP_ID',
            'secret' => 'SECRET_KEY',
            'user_endpoint' => 'https://api.ok.ru/api/users/getCurrentUser?application_key=PUBLIC_KEY',
            'user_callback' => $oauthUserCallback
        ],
    ]
]);
