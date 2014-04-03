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

use FastPay\Error\CardError;

class CardErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $body = '{"error":{"type":"card_error","message":"Your card was declined. Your request was in test mode, but used a no test card.","code":"card_declined","param":null}}';
        $code = "card_declined";
        try {
            throw new CardError(402, $body, $code);
            $this->fail("expect throw card error");
        } catch (CardError $e) {
            $this->assertSame('Status:402, Body:' . $body, $e->getMessage());
            $this->assertSame(402, $e->getHttpStatus());
            $this->assertEquals(json_decode($body), $e->getHttpBody());
            $this->assertSame($code, $e->getCode());
        }
    }
}
