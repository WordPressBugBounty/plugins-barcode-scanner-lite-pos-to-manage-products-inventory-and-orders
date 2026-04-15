<?php

namespace Stripe;

interface BaseStripeClientInterface
{
    public function getApiKey();

    public function getClientId();

    public function getStripeAccount();

    public function getStripeContext();

    public function getStripeVersion();

    public function getApiBase();

    public function getConnectBase();

    public function getFilesBase();

    public function getMeterEventsBase();
}
