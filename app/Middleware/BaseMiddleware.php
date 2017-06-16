<?php

namespace App\Middleware;

use Psr\Container\ContainerInterface;

/**
 * @property  \Slim\Views\Twig          $view
 * @property  \Slim\Flash\Messages      $flash
 * @property  \App\Auth\Auth            $auth
 * @property  \App\Validation\Validator validator
 * @property  \Slim\Router              router
 */
class BaseMiddleware
{
    
    /**
     * @var \Interop\Container\ContainerInterface
     */
    protected $c;
    
    public function __construct(ContainerInterface $container)
    {
        $this->c = $container;
    }
    
    function __get($name)
    {
        if ($this->c->has("{$name}")) {
            return $this->c->{$name};
        }
    }
}