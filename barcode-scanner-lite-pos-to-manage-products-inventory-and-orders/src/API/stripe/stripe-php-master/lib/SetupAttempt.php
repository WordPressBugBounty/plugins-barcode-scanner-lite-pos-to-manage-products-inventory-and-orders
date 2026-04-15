<?php


namespace Stripe;

class SetupAttempt extends ApiResource
{
    const OBJECT_NAME = 'setup_attempt';

    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, Collection::class, $params, $opts);
    }
}
