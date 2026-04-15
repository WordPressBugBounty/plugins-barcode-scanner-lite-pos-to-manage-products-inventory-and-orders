<?php

namespace Stripe\Exception;

class SignatureVerificationException extends \Exception implements ExceptionInterface
{
    protected $httpBody;
    protected $sigHeader;

    public static function factory(
        $message,
        $httpBody = null,
        $sigHeader = null
    ) {
        $instance = new static($message);
        $instance->setHttpBody($httpBody);
        $instance->setSigHeader($sigHeader);

        return $instance;
    }

    public function getHttpBody()
    {
        return $this->httpBody;
    }

    public function setHttpBody($httpBody)
    {
        $this->httpBody = $httpBody;
    }

    public function getSigHeader()
    {
        return $this->sigHeader;
    }

    public function setSigHeader($sigHeader)
    {
        $this->sigHeader = $sigHeader;
    }
}
