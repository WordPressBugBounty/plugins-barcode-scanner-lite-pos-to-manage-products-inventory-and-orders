<?php

namespace Stripe;

interface StripeClientInterface extends BaseStripeClientInterface
{
    public function request($method, $path, $params, $opts);

    public function requestSearchResult($method, $path, $params, $opts);

    public function requestCollection($method, $path, $params, $opts);
}
