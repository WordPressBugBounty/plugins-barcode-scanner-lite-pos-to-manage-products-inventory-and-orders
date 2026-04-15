<?php

namespace Stripe\Exception;

abstract class ApiErrorException extends \Exception implements ExceptionInterface
{
    protected $error;
    protected $httpBody;
    protected $httpHeaders;
    protected $httpStatus;
    protected $jsonBody;
    protected $requestId;
    protected $stripeCode;

    public static function factory(
        $message,
        $httpStatus = null,
        $httpBody = null,
        $jsonBody = null,
        $httpHeaders = null,
        $stripeCode = null
    ) {
        $instance = new static($message);
        $instance->setHttpStatus($httpStatus);
        $instance->setHttpBody($httpBody);
        $instance->setJsonBody($jsonBody);
        $instance->setHttpHeaders($httpHeaders);
        $instance->setStripeCode($stripeCode);

        $instance->setRequestId(null);
        if ($httpHeaders && isset($httpHeaders['Request-Id'])) {
            $instance->setRequestId($httpHeaders['Request-Id']);
        }

        $instance->setError($instance->constructErrorObject());

        return $instance;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function getHttpBody()
    {
        return $this->httpBody;
    }

    public function setHttpBody($httpBody)
    {
        $this->httpBody = $httpBody;
    }

    public function getHttpHeaders()
    {
        return $this->httpHeaders;
    }

    public function setHttpHeaders($httpHeaders)
    {
        $this->httpHeaders = $httpHeaders;
    }

    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    public function setHttpStatus($httpStatus)
    {
        $this->httpStatus = $httpStatus;
    }

    public function getJsonBody()
    {
        return $this->jsonBody;
    }

    public function setJsonBody($jsonBody)
    {
        $this->jsonBody = $jsonBody;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    public function getStripeCode()
    {
        return $this->stripeCode;
    }

    public function setStripeCode($stripeCode)
    {
        $this->stripeCode = $stripeCode;
    }

    public function __toString()
    {
        $parentStr = parent::__toString();
        $statusStr = (null === $this->getHttpStatus()) ? '' : "(Status {$this->getHttpStatus()}) ";
        $idStr = (null === $this->getRequestId()) ? '' : "(Request {$this->getRequestId()}) ";

        return "Error sending request to Stripe: {$statusStr}{$idStr}{$this->getMessage()}\n{$parentStr}";
    }

    protected function constructErrorObject()
    {
        if (null === $this->jsonBody || !\array_key_exists('error', $this->jsonBody)) {
            return null;
        }

        return \Stripe\ErrorObject::constructFrom($this->jsonBody['error']);
    }
}
