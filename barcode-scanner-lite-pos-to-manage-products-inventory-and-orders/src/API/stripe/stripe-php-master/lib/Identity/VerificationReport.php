<?php


namespace Stripe\Identity;

class VerificationReport extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'identity.verification_report';

    const TYPE_DOCUMENT = 'document';
    const TYPE_ID_NUMBER = 'id_number';
    const TYPE_VERIFICATION_FLOW = 'verification_flow';

    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, \Stripe\Collection::class, $params, $opts);
    }

    public static function retrieve($id, $opts = null)
    {
        $opts = \Stripe\Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }
}
