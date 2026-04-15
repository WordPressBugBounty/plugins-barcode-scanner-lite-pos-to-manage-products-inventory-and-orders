<?php

namespace Stripe;

class OAuthErrorObject extends StripeObject
{
    public function refreshFrom($values, $opts, $partial = false, $apiMode = 'v1')
    {
        $values = \array_merge([
            'error' => null,
            'error_description' => null,
        ], $values);
        parent::refreshFrom($values, $opts, $partial);
    }
}
