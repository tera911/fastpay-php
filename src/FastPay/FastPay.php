<?php

/**
 * This file is part of FastPay.
 *
 * Copyright (c) 2014 Yahoo Japan Corporation
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FastPay;

use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;
use FastPay\Api\Charge;
use FastPay\Api\Subscription;

class FastPay
{
    private $apiVersion = "v1";
    private $client = null;

    const CLIENT_VERSION = "1.2.2";

    public function __construct($secret = null)
    {
        $this->client = new Client();
        $this->setupClient();
        $this->setSecret($secret);
    }

    public function __get($action)
    {
        $allowedActions = array("charge", "subscription");
        if (in_array($action, $allowedActions)) {
            return $this->{$action}($this);
        }
    }

    public function charge()
    {
        return new Charge($this->client);
    }

    public function subscription()
    {
        return new Subscription($this->client);
    }

    public function setUrl($url)
    {
        $this->client->setBaseUrl($url);
        return $this;
    }

    public function setSecret($secret)
    {
        $this->client->setDefaultOption('auth', array($secret, '', 'Basic'));
        return $this;
    }

    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
        $this->setupClient();
        return $this;
    }

    public function setPluginUserAgent($pluginName, $pluginVersion)
    {
        $this->client->setUserAgent(
            sprintf('%s/%s FastPay-php/%', $pluginName, $pluginVersion, FastPay::CLIENT_VERSION), true
        );
        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    private function setupClient()
    {
        $this->client
            ->setDescription(ServiceDescription::factory(__DIR__ . "/Resources/{$this->apiVersion}.json"))
            ->setUserAgent("FastPay-php/" . FastPay::CLIENT_VERSION, true);
    }
}
