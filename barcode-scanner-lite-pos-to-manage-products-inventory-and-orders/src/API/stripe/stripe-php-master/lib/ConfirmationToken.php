<?php


namespace Stripe;

class ConfirmationToken extends ApiResource
{
    const OBJECT_NAME = 'confirmation_token';

    const SETUP_FUTURE_USAGE_OFF_SESSION = 'off_session';
    const SETUP_FUTURE_USAGE_ON_SESSION = 'on_session';

    public static function retrieve($id, $opts = null)
    {
        $opts = Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }
}
