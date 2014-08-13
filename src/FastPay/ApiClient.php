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
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\service\Exception\ValidationException;
use Guzzle\Common\Exception\RuntimeException;
use FastPay\Error\ConnectionError;
use FastPay\Error\InvalidRequestError;

class ApiClient extends FastPayObject
{
    private $client;

    public function __construct(Client $client, $values = array())
    {
        parent::__construct($values);

        $this->client = $client;
    }

    public function request($command, array $fields = array())
    {
        $command = $this->client->getCommand($command, $fields);
        $handler = new ResponseHandler($this->client);
        try {
            $command->execute();
            $response = $command->getResponse();
        } catch (ClientErrorResponseException $e) {
            $response = $e->getResponse();
        } catch (ValidationException $e) {
            $response = $e->getResponse();
        } catch (RuntimeException $e) {
            throw new ConnectionError($e->getMessage(), $e->getCode());
        }

        return $handler->parse($response->getStatusCode(), $response->getBody(true));
    }

    public function create(array $fields)
    {
        return $this->request($this->createCommand("create"), $fields);
    }

    public function retrieve($id)
    {
        return $this->request($this->createCommand("retrieve"), array("id" => $id));
    }

    public function createCommand($method)
    {
        return "{$this->getClassName(get_called_class())}.{$method}";
    }

    public function getClassName($name)
    {
        return strtolower(substr($name, strlen("FastPay/Api/")));
    }
}
