<?php

namespace LibClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\JsonResponse;


class RbcService extends Service
{
    /**
     * @var array
     */
    private $sumCurrencies;

    /**
     * @var array
     */
    private $urls;

    /**
     * @var array
     */
    private $nameCurrencies;

    /**
     * RbcService constructor.
     * @param array $urls
     * @param array $nameCurrencies
     */
    public function __construct(array $urls, array $nameCurrencies)
    {
        $this->urls = $urls;
        $this->nameCurrencies = $nameCurrencies;
    }

    /**
     * @param string $url
     * @param UriInterface|null $uri
     * @param RequestInterface|null $request
     * @param ResponseInterface|null $response
     * @param string $method
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getResponse(string $url, UriInterface $uri = null, RequestInterface $request = null,
                               ResponseInterface $response = null, string $method = 'GET'): ResponseInterface
    {
        $request = $this->getRequestInterface($uri, $request, $url, $method);
        try {
            $resp = (new Client(['allow_redirects' => [
                'max'       => 10,       // allow at most 10 redirects.
                'strict'    => true,     // use "strict" RFC compliant redirects.
                'referer'   => true,     // add a Referer header
                'protocols' => ['https'] // only allow https URLs
            ]]))->send($request);
        } catch (ClientException $exception) {
            throw $exception;
        }
        $response = $response ?? new JsonResponse($resp->getBody()->getContents());
        return $response;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function parse(): void
    {
        $sumCurrencies = array_fill_keys($this->nameCurrencies, 0);
        foreach ($this->urls as $currency => $url) {
            /** @var JsonResponse $resp */
            if ($resp = $this->getResponse($url)) {
                $obj = json_decode($resp->getPayload());
                $sumCurrencies[$currency] = floatval($obj->data->sum_result);
            }
        }
        $this->sumCurrencies = $sumCurrencies;
    }

    /**
     * @return mixed
     */
    public function getSumCurrencies()
    {
       return $this->sumCurrencies;
    }

}
