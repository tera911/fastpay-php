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
use FastPay\Api\Card;
use FastPay\Api\Charge;
use FastPay\Error\FastPayError;
use FastPay\Error\ApiError;
use FastPay\Error\CardError;
use FastPay\Error\ConnectionError;
use FastPay\Error\InvalidRequestError;

class ResponseHandler
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function parse($status, $body)
    {
        $response = json_decode($body, true);

        if (isset($response["error"]["type"])) {
            switch ($response["error"]["type"]) {
                case 'card_error':
                    throw new CardError($status, $body, $response["error"]["code"]);
                    break;
                case 'api_error':
                    throw new ApiError($status, $body);
                    break;
                case 'invalid_request_error':
                    throw new InvalidRequestError($status, $body);
                    break;
                default:
                    throw new ConnectionError("Unexpected JSON Data. Body:" . $body);
                    break;
            }
        }

        if (is_null($response) || ! isset($response["object"])) {
            throw new FastPayError($status, $body);
        }

        return $this->convertFastPayObject($response);
    }

    public function convertFastPayObject($object)
    {
        $fastpayObjects = array(
            'FastPay\FastPayObject',
            'FastPay\Api\Card',
        );
        if (is_array($object)) {
            if (isset($object["object"]) && $object["object"] === "list") {
                $lists = array();
                foreach ($object['data'] as $v) {
                    $lists[] = $this->convertFastPayObject($v);
                }
                return $lists;
            }

            foreach ($object as $key => $value) {
                if (! is_null($this->mapToObject($key))) {
                    $object[$key] = $this->convertFastPayObject($value);
                }
            }
            if (isset($object["object"])) {
                $className = $this->mapToObject($object["object"]);
                if (in_array($className, $fastpayObjects)) {
                    return new $className($object);
                }
                return new $className($this->client, $object);
            }
        }
        return $object;
    }

    private function mapToObject($name)
    {
        $classes = array(
            "charge" => 'FastPay\Api\Charge',
            "card" => 'FastPay\Api\Card',
        );

        return array_key_exists($name, $classes) ?  $classes[$name] : 'FastPay\FastPayObject';
    }
}
