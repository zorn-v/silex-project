<?php

$app->get('/login', 'App\\Controller\\Auth::login')->bind('login');
$app->get('/', 'App\\Controller\\Sample::index')->bind('index');
