<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @property  \Predis\Client $redis
 */
class SomeController extends BaseController
{
    
    public function index(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        return $response->withJson([
            'data' => true,
        ]);
        
    }
    
    
}