<?php

namespace Stripe\Exception;

class InvalidRequestException extends ApiErrorException
{
    protected $stripeParam;

    public static function factory(
        $message,
        $httpStatus = null,
        $httpBody = null,
        $jsonBody = null,
        $httpHeaders = null,
        $stripeCode = null,
        $stripeParam = null
    ) {
        $instance = parent::factory($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders, $stripeCode);
        $instance->setStripeParam($stripeParam);

        return $instance;
    }

    public function getStripeParam()
    {
        return $this->stripeParam;
    }

    public function setStripeParam($stripeParam)
    {
        $this->stripeParam = $stripeParam;
    }
}
