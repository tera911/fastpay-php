<?php

/**
 * This file is part of FastPay.
 *
 * Copyright (c) 2014 Yahoo Japan Corporation
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FastPay\Tests\Error;

use FastPay\Error\FastPayError;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        try {
            throw new FastPayError(500, '{"foo":"bar"}');
            $this->fail("Did not raise error");
        } catch (FastPayError $e) {
            $this->assertSame("Status:500, Body:{\"foo\":\"bar\"}", $e->getMessage());
            $this->assertSame(500, $e->getHttpStatus());
            $this->assertEquals(json_decode('{"foo":"bar"}'), $e->getHttpBody());
        }
    }
}
