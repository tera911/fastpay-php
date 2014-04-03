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

use FastPay\FastPay;
use Guzzle\Plugin\Mock\MockPlugin;

class FastPayTestCase extends \PHPUnit_Framework_TestCase
{
    protected $fastpay;
    protected $plugin;

    public function __construct()
    {
        $this->fastpay = new FastPay("SecretID");
        $this->fastpay->setUrl("https://example.com");
    }

    public function setMock($action)
    {
        $this->plugin = new MockPlugin();
        $this->plugin->addResponse(__DIR__ . "/../../mock/{$action}.txt");
        $this->fastpay->getClient()->addSubscriber($this->plugin);
    }

    public function assertGet($action, $params = null)
    {
        $request = $this->plugin->getReceivedRequests();
        $request = $request[0];
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals('/v1' . $action, $request->getPath());
        $this->assertEquals('SecretID', $request->getUsername());

        if (! is_null($params) && is_array($params)) {
            $this->assertEquals($params, $request->getQuery()->toArray());
        }
    }

    public function assertPost($action, $params = null)
    {
        $request = $this->plugin->getReceivedRequests();
        $request = $request[0];
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals('/v1' . $action, $request->getPath());
        $this->assertEquals('SecretID', $request->getUsername());

        if (! is_null($params) && is_array($params)) {
            $this->assertEquals($params, $request->getPostFields()->toArray());
        }
    }
}
