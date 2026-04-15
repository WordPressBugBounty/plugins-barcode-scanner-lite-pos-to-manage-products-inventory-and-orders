<?php

namespace Stripe;

abstract class Webhook
{
    const DEFAULT_TOLERANCE = 300;

    public static function constructEvent($payload, $sigHeader, $secret, $tolerance = self::DEFAULT_TOLERANCE)
    {
        WebhookSignature::verifyHeader($payload, $sigHeader, $secret, $tolerance);

        $data = \json_decode($payload, true);
        $jsonError = \json_last_error();
        if (null === $data && \JSON_ERROR_NONE !== $jsonError) {
            $msg = "Invalid payload: {$payload} "
              . "(json_last_error() was {$jsonError})";

            throw new Exception\UnexpectedValueException($msg);
        }

        return Event::constructFrom($data);
    }
}
