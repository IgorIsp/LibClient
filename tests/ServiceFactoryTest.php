<?php

namespace LibClient\Tests;

use LibClient\ServiceFactory;
use PHPUnit\Framework\TestCase;


class ServiceFactoryTest extends TestCase
{
    public $serviceFactory;

    protected function setUp()
    {
        $this->serviceFactory = new ServiceFactory();
        $this->serviceFactory->needCurrencies = ['USD', 'EUR', 'GBP'];
        $this->serviceFactory->date = new \DateTime('2018-04-11');
        parent::setUp();
    }

    public function testCalcAverage()
    {
        $this->serviceFactory->createRbcService()->parse();
        $this->serviceFactory->createSbrService()->parse();
        $expected = ['USD' => '62.3699', 'EUR' => '76.8522', 'GBP' => '88.2971'];
        $actual = $this->serviceFactory->getCalcAverage();
        self::assertEquals($expected, $actual, json_encode($expected) . ' <> ' . json_encode($actual));
    }

    public function testCreateRbcService()
    {
        $rbc = $this->serviceFactory->createRbcService();
        self::assertEquals('LibClient\RbcService',
            get_class($rbc), '"LibClient\RbcService" <> "' . get_class($rbc) . '"');
    }

    public function testCreateSbrService()
    {
        $sbr = $this->serviceFactory->createSbrService();
        self::assertEquals('LibClient\SbrService',
            get_class($sbr), '"LibClient\SbrService" <> "' . get_class($sbr) . '"');
    }
}
