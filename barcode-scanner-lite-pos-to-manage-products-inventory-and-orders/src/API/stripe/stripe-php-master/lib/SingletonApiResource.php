<?php

namespace Stripe;

abstract class SingletonApiResource extends ApiResource
{
    public static function classUrl()
    {

        $base = \str_replace('.', '/', static::OBJECT_NAME);

        return "/v1/{$base}";
    }

    public function instanceUrl()
    {
        return static::classUrl();
    }
}
