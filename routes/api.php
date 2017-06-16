<?php

$app->group('/api', function () {
   $this->get('', \App\Controllers\SomeController::class . ':index') ;
})->add(new \App\Middleware\LimitRequests($container));
