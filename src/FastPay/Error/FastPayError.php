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

class FastPayError extends \Exception
{
    public function __construct($httpStatus = null, $httpBody = null)
    {
        $message = sprintf("Status:%s, Body:%s", $httpStatus, $httpBody);
        parent::__construct($message);
        $this->httpStatus = $httpStatus;
        $this->httpBody = json_decode($httpBody);
    }

    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    public function getHttpBody()
    {
        return $this->httpBody;
    }

    public function __toString()
    {
        return get_class($this) . " (Status " . $this->httpStatus . ") " . json_encode($this->httpBody);
    }
}
