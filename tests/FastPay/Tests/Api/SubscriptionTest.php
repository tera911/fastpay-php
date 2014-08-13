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

//use FastPay\Error\InvalidRequestError;
//use FastPay\Error\CardError;

class SubscriptionTest extends \FastPay\Tests\FastPayTestCase
{
    public function testActivate()
    {
        $this->setMock("subscription/activate");
        $params = array(
            "subscription_id" => "subs_xxxxxxxxxxxxxxxxxxxxxxxx",
            "description" => "fastpay@example.com",
        );
        $actual = $this->fastpay->subscription()->activate($params);
        $this->assertInstanceOf("FastPay\Api\Subscription", $actual);
        $this->assertRegExp('/^subs_[A-Za-z0-9]+$/', $actual->id);

        //$this->assertPost("/subscription/{$params[subscription_id]}/activate", $params);
        $this->assertPost("/subscription/subs_xxxxxxxxxxxxxxxxxxxxxxxx/activate", array("description" => "fastpay@example.com"));
    }
}
