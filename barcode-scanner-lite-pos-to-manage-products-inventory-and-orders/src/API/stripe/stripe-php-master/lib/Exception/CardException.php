<?php

namespace Stripe\Exception;

class CardException extends ApiErrorException
{
    protected $declineCode;
    protected $stripeParam;

    public static function factory(
        $message,
        $httpStatus = null,
        $httpBody = null,
        $jsonBody = null,
        $httpHeaders = null,
        $stripeCode = null,
        $declineCode = null,
        $stripeParam = null
    ) {
        $instance = parent::factory($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders, $stripeCode);
        $instance->setDeclineCode($declineCode);
        $instance->setStripeParam($stripeParam);

        return $instance;
    }

    public function getDeclineCode()
    {
        return $this->declineCode;
    }

    public function setDeclineCode($declineCode)
    {
        $this->declineCode = $declineCode;
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
