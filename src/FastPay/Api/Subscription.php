<?php

/**
 * This file is part of FastPay.
 *
 * Copyright (c) 2014 Yahoo Japan Corporation
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FastPay\Api;

use FastPay\ApiClient;

class Subscription extends ApiClient
{
    public function activate(array $fields = array())
    {
        return $this->request($this->createCommand("activate"), $fields);
    }
}
