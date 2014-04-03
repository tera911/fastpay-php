<?php

/**
 * This file is part of FastPay.
 *
 * Copyright (c) 2014 Yahoo Japan Corporation
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FastPay\Tests\Api;

use FastPay\Error\InvalidRequestError;
use FastPay\Error\CardError;

class ChargeTest extends \FastPay\Tests\FastPayTestCase
{
    public function testChargeCreate()
    {
        $this->setMock("charges/create");
        $params = array(
            "amount" => 7777,
            "card" => "tok_xxxxxxxxxxxxxxxxxxxxx",
            "description" => "fastpay@example.com",
        );

        $actual = $this->fastpay->charge->create($params);
        $this->assertInstanceOf("FastPay\Api\Charge", $actual);
        $this->assertRegExp('/^ch_[A-Za-z0-9]+$/', $actual->id);

        $this->assertInstanceOf("FastPay\Api\Card", $actual->card);
        $this->assertRegExp('/^card_[A-Za-z0-9]+$/', $actual->card->id);

        $this->assertPost("/charges", $params);
    }

    public function testChargeRetrieve()
    {
        $this->setMock("charges/retrieve");
        $id = "ch_xxxxxxxxxx";
        $actual = $this->fastpay->charge->retrieve($id);

        $this->assertInstanceOf("FastPay\Api\Charge", $actual);
        $this->assertRegExp('/^ch_[A-Za-z0-9]+$/', $actual->id);

        $this->assertInstanceOf("FastPay\Api\Card", $actual->card);
        $this->assertRegExp('/^card_[A-Za-z0-9]+$/', $actual->card->id);

        $this->assertGet("/charges/{$id}");
    }

    public function testChargeAll()
    {
        $this->setMock("charges/all");
        $params = array(
            "count" => 3,
        );

        $charges = $this->fastpay->charge->all($params);

        $actual = $charges[0];
        $this->assertInstanceOf("FastPay\Api\Charge", $actual);
        $this->assertRegExp('/^ch_[A-Za-z0-9]+$/', $actual->id);

        $actual = $charges[1];
        $this->assertInstanceOf("FastPay\Api\Card", $actual->card);
        $this->assertRegExp('/^card_[A-Za-z0-9]+$/', $actual->card->id);

        $this->assertGet("/charges", $params);
    }

    public function testChargeRefund()
    {
        $this->setMock("charges/create");
        $charge = $this->fastpay->charge->create(array(
            "amount" => 7777,
            "card" => "tok_xxxxxxxxxxxxxxxxxxxxx",
            "description" => "fastpay@example.com",
        ));

        $this->setMock("charges/refund");
        $actual = $charge->refund();

        $this->assertRegExp('/^card_[A-Za-z0-9]+$/', $actual->card->id);

        $this->assertPost("/charges/ch_4Iv1CIKeIJQiF1Yg1tjWZejT/refund");
    }

    public function testChargeCapture()
    {
        $this->setMock("charges/create");
        $charge = $this->fastpay->charge->create(array(
            "amount" => 7777,
            "card" => "tok_xxxxxxxxxxxxxxxxxxxxx",
            "description" => "fastpay@example.com",
        ));

        $this->setMock("charges/capture");
        $actual = $charge->capture();

        $this->assertRegExp('/^card_[A-Za-z0-9]+$/', $actual->card->id);

        $this->assertPost("/charges/ch_4Iv1CIKeIJQiF1Yg1tjWZejT/capture");
    }

    public function testChargeCreateInvalidToken()
    {
        $this->setMock("errors/invalid_token");
        $body = '{"error":{"type":"card_error","message":"Invalid token id: tok_xxxxxxxxxxxxx","code":"","param":""}}';
        try {
            $this->fastpay->charge->create(array(
                "amount" => 7777,
                "card" => "tok_invalid",
                "description" => "fastpay@example.com",
            ));
            $this->fail("invalid card id");
        } catch (CardError $e) {
            $this->assertSame(402, $e->getHttpStatus());
            $this->assertSame("Status:402, Body:$body", $e->getMessage());
            $this->assertEquals(json_decode($body), $e->getHttpBody());
        }
    }

    public function testChargeRetrieveInvalidChargeId()
    {
        $this->setMock("errors/token_not_found");
        $body = '{"error":{"type":"invalid_request_error","message":"No such charge: tok_xxxxxxxxxxxxxx","code":null,"param":"id"}}';
        try {
            $this->fastpay->charge->retrieve("tok_xxxxxxxxxxxxxx");
            $this->fail("invalid charge id");
        } catch (InvalidRequestError $e) {
            $this->assertSame(404, $e->getHttpStatus());
            $this->assertSame("Status:404, Body:$body", $e->getMessage());
            $this->assertEquals(json_decode($body), $e->getHttpBody());
        }
    }
}
