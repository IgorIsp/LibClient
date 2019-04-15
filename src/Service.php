<?php

namespace LibClient;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;


abstract class Service
{

    public function parse(): void {}

    /**
     * @param UriInterface|null $uri
     * @param RequestInterface|null $request
     * @param string $url
     * @param string $method
     * @return RequestInterface
     */
    public function getRequestInterface(UriInterface $uri = null, RequestInterface $request = null,
                                        string $url = '', string $method = 'GET'): RequestInterface
    {
        $uri = $uri ?? new Uri($url);
        return $request ?? (new Request())->withUri($uri)->withMethod($method);
    }

}
