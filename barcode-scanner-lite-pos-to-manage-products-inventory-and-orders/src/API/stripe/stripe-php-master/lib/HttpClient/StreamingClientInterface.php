<?php

namespace Stripe\HttpClient;

interface StreamingClientInterface
{
    public function requestStream($method, $absUrl, $headers, $params, $hasFile, $readBodyChunkCallable, $maxNetworkRetries = null);
}
