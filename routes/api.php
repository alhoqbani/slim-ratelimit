<?php

$limiter = new \App\Middleware\LimitRequests($container['redis']);

$limiter->setRateLimit(10, 30)
//    ->setIdentifier(100)
    ->setLimitExcededHandler(function ($req, $res, $next) {
        return $res->withJson([
            'errors' => [
                'Status' => 429,
                'title'  => 'Too many request, please try in 60 minutes',
            ],
        ], 429);
    })
    ->setStorageKey('rate:%s:requests');

$app->group('/api', function () {
    $this->get('', \App\Controllers\SomeController::class . ':index');
})->add($limiter);
