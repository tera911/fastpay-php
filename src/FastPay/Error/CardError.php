<?php

/**
 * This file is part of FastPay.
 *
 * Copyright (c) 2014 Yahoo Japan Corporation
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FastPay\Error;

class CardError extends FastPayError
{
    public function __construct($httpStatus = null, $httpBody = null, $code = null)
    {
        parent::__construct($httpStatus, $httpBody);
        $this->code = $code;
    }
}
