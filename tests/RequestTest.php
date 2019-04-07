<?php

namespace LibClient\Tests;

use LibClient;
use LibClient\Request;
use PHPUnit\Framework\TestCase;

/**
 * @covers LibClient\Request
 */
class RequestTest extends TestCase
{
    public function testRequest()
    {
        $r = new Request();
        $class = $r->getClass();
        $this->assertEquals('LibClient\Request', $class, 'Test Request() don\'t passed! ' . $class);
    }


}