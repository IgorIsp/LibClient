<?php

namespace LibClient\Tests;

use GuzzleHttp\Client;
use LibClient\SbrService;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Response\XmlResponse;


class SbrServiceTest extends TestCase
{
    public $link;

    public $needCurrencies;

    protected function setUp()
    {
        $dateStr = (new \DateTime('2018-04-11'))->format('d/m/Y');
        $this->needCurrencies = ['USD', 'EUR'];
        $this->link = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $dateStr;
        parent::setUp();
    }

    public function testGetStream()
    {
        $sbr = new SbrService($this->link, $this->needCurrencies);
        $request = $sbr->getRequestInterface(null, null, $this->link);
        $resp = (new Client([]))->send($request);
        $response = $response ?? new XmlResponse($resp->getBody()->getContents());
        $stream = $response->getBody();
        self::assertEquals('Zend\Diactoros\Stream',
            get_class($stream), '"Zend\Diactoros\Stream" <> "' . get_class($stream) . '"');
    }

    public function testParse()
    {
        $sbr = new SbrService($this->link, $this->needCurrencies);
        $sbr->parse();
        self::assertEquals(62.3699, $sbr->getSumCurrencies()['USD'],  '62.3699 <> ' . $sbr->getSumCurrencies()['USD']);
        self::assertEquals(76.8522, $sbr->getSumCurrencies()['EUR'],  '76.8522 <> ' . $sbr->getSumCurrencies()['EUR']);
    }
}
