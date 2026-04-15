<?php

namespace Stripe\HttpClient;

interface ClientInterface
{
    public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null);
}
