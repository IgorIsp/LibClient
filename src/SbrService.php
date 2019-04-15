<?php

namespace LibClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use SimpleXMLElement;
use Zend\Diactoros\Response\XmlResponse;


class SbrService extends Service
{
    /**
     * @var array
     */
    private $sumCurrencies;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $nameCurrencies;

    /**
     * SbrService constructor.
     * @param string $url
     * @param array $nameCurrencies
     */
    public function __construct(string $url, array $nameCurrencies)
    {
        $this->url = $url;
        $this->nameCurrencies = $nameCurrencies;
    }

    /**
     * @param UriInterface|null $uri
     * @param RequestInterface|null $request
     * @param ResponseInterface|null $response
     * @param string $method
     * @return StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStream(UriInterface $uri = null, RequestInterface $request = null,
                               ResponseInterface $response = null, string $method = 'GET'): StreamInterface
    {
        $request = $this->getRequestInterface($uri, $request, $this->url, $method);
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
        $response = $response ?? new XmlResponse($resp->getBody()->getContents());
        return $response->getBody();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function parse(): void
    {
        $sumCurrencies = array_fill_keys($this->nameCurrencies, 0);
        if ($stream = $this->getStream()) {
            $curs = new SimpleXMLElement($stream->getContents());
            foreach ($curs->Valute as $valute) {
                $currency = (string)$valute->CharCode;
                $sumCurrencies[$currency] = floatval(str_replace(',', '.', (string)$valute->Value));
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
