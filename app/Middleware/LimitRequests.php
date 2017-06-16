<?php

namespace App\Middleware;

use Predis\Client as Redis;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LimitRequests
{
    protected $requests = 30;
    protected $perSecond = 60;
    protected $identifier;
    protected $limitExcededHandler = null;
    protected $storageKey = 'rate:%s:requests';
    
    /**
     * @var \Predis\Client
     */
    protected $redis;
    
    /**
     * LimitRequests constructor.
     *
     * @param \Predis\Client $redis
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
        $this->identifier = $this->getIdentifier();
    }
    
    /**
     *
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if ($this->hasExceededRateLimit()) {
            return $this->getLimitExcededHandler()($request, $response, $next);
        }
        $this->incrementRequestsCount();
        
        return $next($request, $response);
    }
    
    public function defaultLimitExcededHandler()
    {
        return function (Request $request, Response $response, $next) {
            return $response->withJson(['error' => 'Too many requests, slow down !!'], 429);
        };
    }
    
    /**
     * @param int $requests
     * @param int $perSecond
     *
     * @return $this
     */
    public function setRateLimit(int $requests, int $perSecond)
    {
        $this->requests = $requests;
        $this->perSecond = $perSecond;
        
        return $this;
    }
    
    /**
     * @param string $storageKey
     *
     * @return $this
     */
    public function setStorageKey(string $storageKey)
    {
        $this->storageKey = $storageKey;
        
        return $this;
    }
    
    /**
     * @param string|mixed $identifier
     *
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        
        return $this;
    }
    
    /**
     * @param callable|null $handler
     *
     * @return \App\Middleware\LimitRequests
     */
    public function setLimitExcededHandler(callable $handler)
    {
        $this->limitExcededHandler = $handler;
        
        return $this;
    }
    
    /**
     * @param null $identifier
     *
     * @return mixed
     */
    protected function getIdentifier($identifier = null)
    {
        if ($identifier === null) {
            return $_SERVER['REMOTE_ADDR'];
        }
        
        return $identifier;
    }
    
    protected function getLimitExcededHandler()
    {
        if ($this->limitExcededHandler === null) {
            return $this->defaultLimitExcededHandler();
        }
        
        return $this->limitExcededHandler;
    }
    
    protected function incrementRequestsCount()
    {
        $key = $this->getStorageKey();
        $this->redis->incr($key);
        $this->redis->expire($key, $this->perSecond);
    }
    
    protected function hasExceededRateLimit()
    {
        $key = $this->getStorageKey();
        $value = $this->redis->get($key);
        
        return $value >= $this->requests;
    }
    
    /**
     * @return string
     */
    protected function getStorageKey(): string
    {
        $key = sprintf($this->storageKey, $this->identifier);
        
        return $key;
    }
}
