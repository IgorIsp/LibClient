<?php

namespace LibClient\Publics;

use LibClient\ServiceFactory;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$request = ServerRequestFactory::fromGlobals();
$name = $request->getQueryParams()['name'] ?? 'Guest';


$serviceFactory = new ServiceFactory();
$serviceFactory->needCurrencies = ['USD', 'EUR', 'GBP'];
$serviceFactory->date = new \DateTime('2018-04-11');
$serviceFactory->createRbcService()->parse();
$serviceFactory->createSbrService()->parse();

$table = '<table><thead><th>Currency</th><th>Value</th></thead><tbody>';
foreach ($serviceFactory->getCalcAverage() as $currency => $value) {
    $table .= "<tr><td>{$currency}</td><td>{$value}</td></tr>";
}
$table .= '</tbody></table>';

$response = (new HtmlResponse("<h1> Hello, {$name}</h1>$table"))
    ->withHeader('X-Dev', "Ispir");

$emitter = new SapiEmitter();
$emitter->emit($response);