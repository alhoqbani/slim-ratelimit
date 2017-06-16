<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;

class BaseController
{
    
    protected $c;
    
    public function __construct(ContainerInterface $container)
    {
        $this->c = $container;
    }
    
    public function __get($name)
    {
        if ($this->c->has($name)) {
            return $this->c->{$name};
        }
    }
    
}