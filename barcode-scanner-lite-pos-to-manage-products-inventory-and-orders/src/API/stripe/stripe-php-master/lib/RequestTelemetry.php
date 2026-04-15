<?php

namespace Stripe;

class RequestTelemetry
{
    public $requestId;
    public $requestDuration;
    public $usage;

    public function __construct($requestId, $requestDuration, $usage = [])
    {
        $this->requestId = $requestId;
        $this->requestDuration = $requestDuration;
        $this->usage = $usage;
    }
}
