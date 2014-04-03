<?php

/**
 * This file is part of FastPay.
 *
 * Copyright (c) 2014 Yahoo Japan Corporation
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FastPay\Tests;

use Guzzle\Service\Client;
use FastPay\FastPay;
use FastPay\ResponseHandler;

class ResponseHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $handler;

    public function setup()
    {
        $this->handler = new ResponseHandler(new Client);
    }

    public function testConvertToFastPayObject()
    {
        $input = json_decode('{"id":"72cc29a6f37c0e035e9df82b","object":"charge","livemode":false,"currency":"jpy","description":"fastpay@example.com","amount":666,"amount_refunded":null,"customer":null,"created":1389333100,"paid":"1","refunded":false,"failure_message":null,"card":{"id":"d7c448c8d88da8b703250375","object":"card","last4":"0113","type":"Visa","exp_month":"11","exp_year":"2014","fingerprint":"","country":"","cvc_check":"unchecked"},"captured":true,"refunds":[],"expire_time":null}', true);

        $actual = $this->handler->convertFastPayObject($input);
        $this->assertInstanceOf("FastPay\Api\Charge", $actual);
        $this->assertSame("72cc29a6f37c0e035e9df82b", $actual->id);
        $this->assertSame(false, $actual->livemode);
        $this->assertSame("jpy", $actual->currency);
        $this->assertSame("fastpay@example.com", $actual->description);
        $this->assertSame(666, $actual->amount);
        $this->assertSame(null, $actual->amount_refunded);
        $this->assertSame(null, $actual->customer);
        $this->assertSame("1", $actual->paid);
        $this->assertSame(false, $actual->refunded);
        $this->assertSame(null, $actual->failure_message);
        $this->assertSame(true, $actual->captured);
        $this->assertSame(array(), $actual->refunds);
        $this->assertSame(null, $actual->expire_time);

        $this->assertInstanceOf("FastPay\Api\Card", $actual->card);
        $this->assertSame("d7c448c8d88da8b703250375", $actual->card->id);
        $this->assertSame("0113", $actual->card->last4);
        $this->assertSame("Visa", $actual->card->type);
        $this->assertSame("11", $actual->card->exp_month);
        $this->assertSame("2014", $actual->card->exp_year);
        $this->assertSame("", $actual->card->fingerprint);
        $this->assertSame("", $actual->card->country);
        $this->assertSame("unchecked", $actual->card->cvc_check);
    }

    public function testTransformToFastPayObjectLists()
    {
        $input = json_decode('{"object":"list","url":"\/v1\/charges","count":3,"data":[{"id":"7ce048185b5b21f4db94a8ff","object":"charge","created":1389944282,"livemode":true,"paid":true,"amount":"888","currency":"JPY","refunded":false,"card":{"id":"776336be6e60fdb219f45705","object":"card","last4":"0113","type":"Visa","exp_month":"11","exp_year":"2014","fingerprint":null,"country":null,"cvc_check":null},"captured":true,"failure_message":"0","failure_code":null,"amount_refunded":"0","description":"fastpay@example.com"},{"id":"b284c617a93181b682a52496","object":"charge","created":1389944270,"livemode":true,"paid":true,"amount":"888","currency":"JPY","refunded":false,"card":{"id":"52f46239589fa7f7e1d2cd39","object":"card","last4":"0113","type":"Visa","exp_month":"11","exp_year":"2014","fingerprint":null,"country":null,"cvc_check":null},"captured":true,"failure_message":"0","failure_code":null,"amount_refunded":"0","description":"fastpay@example.com"},{"id":"653a18fca5aeaa855fc79909","object":"charge","created":1389941536,"livemode":true,"paid":true,"amount":"10000","currency":"JPY","refunded":false,"card":{"id":"3e95ae90f4a239abe7e7cdef","object":"card","last4":"0113","type":"Visa","exp_month":"11","exp_year":"2014","fingerprint":null,"country":null,"cvc_check":null},"captured":true,"failure_message":"0","failure_code":null,"amount_refunded":"0","description":"fastpay@example.com"}]}', true);

        $obj = $this->handler->convertFastPayObject($input);
        $actual = $obj[0];
        $this->assertInstanceOf("FastPay\Api\Charge", $actual);
        $this->assertSame("7ce048185b5b21f4db94a8ff", $actual->id);
        $this->assertSame(true, $actual->livemode);
        $this->assertSame("JPY", $actual->currency);

        $this->assertInstanceOf("FastPay\Api\Card", $actual->card);
        $this->assertSame("776336be6e60fdb219f45705", $actual->card->id);
        $this->assertSame("0113", $actual->card->last4);

        $actual = $obj[1];
        $this->assertInstanceOf("FastPay\Api\Charge", $actual);
        $this->assertSame("b284c617a93181b682a52496", $actual->id);
        $this->assertSame(true, $actual->livemode);
        $this->assertSame("JPY", $actual->currency);

        $this->assertInstanceOf("FastPay\Api\Card", $actual->card);
        $this->assertSame("52f46239589fa7f7e1d2cd39", $actual->card->id);
        $this->assertSame("0113", $actual->card->last4);
    }

    public function testConvertToFastPayObjectToken()
    {
        $input = json_decode('{"id":"tok_103Tm22eZvKYlo2CyuvbzQHQ","livemode":false,"created":1392155694,"used":false,"object":"token","type":"card","card":{"id":"card_103Tm22eZvKYlo2CiZtdMpep","object":"card","last4":"4242","type":"Visa","exp_month":8,"exp_year":2015,"fingerprint":"Xt5EWLLDS7FJjR1c","customer":null,"country":"US","name":null,"address_line1":null,"address_line2":null,"address_city":null,"address_state":null,"address_zip":null,"address_country":null}}', true);

        $actual = $this->handler->convertFastPayObject($input);
        $this->assertInstanceOf("FastPay\FastPayObject", $actual);

        $this->assertSame("Visa", $actual->card->type);
        $this->assertSame("4242", $actual->card->last4);
        $this->assertSame(8, $actual->card->exp_month);
        $this->assertSame(2015, $actual->card->exp_year);
    }

    public function testParserThrowCardError()
    {

        $body = '{"error":{"type":"card_error","message":"dummy processing error","code":"processing_error","param":null}}';
        $info = 402;

        $actual_to_string = "FastPay\Error\CardError (Status 402) " . $body;

        try {
            $this->handler->parse($info, $body);
            $this->fail('parser error expected CardError Exception');
        } catch (\FastPay\Error\CardError $e) {
            $this->assertSame("Status:402, Body:" . $body, $e->getMessage());
            $this->assertSame(402, $e->getHttpStatus());
            $this->assertEquals(json_decode($body), $e->getHttpBody());
            $this->assertSame($actual_to_string, (string) $e);
        }
    }

    public function testParserThrowInvalidRequestError()
    {

        $body = '{"error":{"type":"invalid_request_error","message":"No such charge: 00caf063cb458bc555127320","code":null,"param":"id"}}';
        $info = 404;

        $actual_to_string = "FastPay\Error\InvalidRequestError (Status 404) " . $body;

        try {
            $this->handler->parse($info, $body);
            $this->fail('parser error expected InvalidRequestError Exception');
        } catch (\FastPay\Error\InvalidRequestError $e) {
            $this->assertSame("Status:404, Body:" . $body, $e->getMessage());
            $this->assertSame(404, $e->getHttpStatus());
            $this->assertEquals(json_decode($body), $e->getHttpBody());
            $this->assertSame($actual_to_string, (string) $e);
        }
    }

    public function testInvalidJSONData()
    {
        $body = 'invalid json data';
        $info = 200;

        try {
            $this->handler->parse($info, $body);
            $this->fail('parser error expected FastPayError Exception');
        } catch (\FastPay\Error\FastPayError $e) {
            $this->assertSame("Status:200, Body:" . $body, $e->getMessage());
        }
    }

    public function testUnexpectedResponse()
    {
        $body = '{"gohan": "okazu"}';
        $info = 200;

        try {
            $this->handler->parse($info, $body);
            $this->fail('parser error expected FastPayError Exception');
        } catch (\FastPay\Error\FastPayError $e) {
            $this->assertSame("Status:200, Body:" . $body, $e->getMessage());
        }
    }

    public function testUnexpectedJsonData()
    {
        $body = '{"error": {"type": "gohan"}}';
        $info = 400;

        try {
            $this->handler->parse($info, $body);
            $this->fail('parser error expected ConnectionError Exception');
        } catch (\FastPay\Error\ConnectionError $e) {
            $this->assertSame("Unexpected JSON Data. Body:" . $body, $e->getMessage());
        }
    }
}
