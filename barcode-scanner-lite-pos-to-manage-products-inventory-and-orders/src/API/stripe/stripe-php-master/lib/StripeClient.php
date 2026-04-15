<?php

namespace Stripe;

class StripeClient extends BaseStripeClient
{
    private $coreServiceFactory;

    public function __get($name)
    {
        return $this->getService($name);
    }

    public function getService($name)
    {
        if (null === $this->coreServiceFactory) {
            $this->coreServiceFactory = new Service\CoreServiceFactory($this);
        }

        return $this->coreServiceFactory->getService($name);
    }
}
