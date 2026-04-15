<?php

namespace Stripe\Service;

abstract class AbstractServiceFactory
{
    use ServiceNavigatorTrait;

    public function __construct($client)
    {
        $this->client = $client;
    }
}
