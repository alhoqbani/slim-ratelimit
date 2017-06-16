<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
]);

$container = $app->getContainer();

$container['redis'] = function ($c) {
    return new Predis\Client([
        'scheme'   => 'tcp',
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'passowrd' => null,
    ]);
    
};

require __DIR__ . '/../routes/api.php';