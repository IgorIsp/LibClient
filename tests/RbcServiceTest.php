<?php

namespace LibClient\Tests;

use GuzzleHttp\Client;
use LibClient\RbcService;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Response\JsonResponse;


class RbcServiceTest extends TestCase
{
    public $links;

    public $needCurrencies;

    protected function setUp()
    {
        $dateStr = (new \DateTime('2018-04-11'))->format('Y-m-d');
        $this->needCurrencies = ['USD', 'EUR'];
        $this->links = [];
        foreach ($this->needCurrencies as $currency) {
            $this->links[$currency] =
                "https://cash.rbc.ru/cash/json/converter_currency_rate/?currency_from={$currency}&currency_to=RUR&source=cbrf&sum=1&date={$dateStr}";
        }
        parent::setUp();
    }

    public function testGetResponse()
    {
        $rbc = new RbcService($this->links, $this->needCurrencies);
        $responses = [];
        foreach ($this->links as $currency => $url) {
            $request = $rbc->getRequestInterface(null, null, $url);
            $resp = (new Client([]))->send($request);
            $response = $response ?? new JsonResponse($resp->getBody()->getContents());
            $responses[$currency] = $response;
            $response = null;
        }
        self::assertEquals('Zend\Diactoros\Response\JsonResponse', get_class($responses['USD']),
            '"Zend\Diactoros\Response\JsonResponse" <> "' . get_class($responses['USD']) . '"');
        $usd = json_decode($responses['USD']->getPayload());
        $eur = json_decode($responses['EUR']->getPayload());
        self::assertEquals('USD', $usd->meta->currency_from);
        self::assertEquals('EUR', $eur->meta->currency_from);
    }

    public function testParse()
    {
        $rbc = new RbcService($this->links, $this->needCurrencies);
        $rbc->parse();
        $expected = ['USD' => '62.3699', 'EUR' => '76.8522'];
        $actual = $rbc->getSumCurrencies();
        self::assertEquals($expected, $actual, json_encode($expected) . ' <> ' . json_encode($actual));
    }
}
