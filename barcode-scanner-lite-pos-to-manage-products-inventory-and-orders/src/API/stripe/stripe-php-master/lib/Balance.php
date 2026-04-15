<?php


namespace Stripe;

class Balance extends SingletonApiResource
{
    const OBJECT_NAME = 'balance';

    public static function retrieve($opts = null)
    {
        $opts = Util\RequestOptions::parse($opts);
        $instance = new static(null, $opts);
        $instance->refresh();

        return $instance;
    }
}
