<?php


namespace Stripe\Radar;

class EarlyFraudWarning extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'radar.early_fraud_warning';

    const FRAUD_TYPE_CARD_NEVER_RECEIVED = 'card_never_received';
    const FRAUD_TYPE_FRAUDULENT_CARD_APPLICATION = 'fraudulent_card_application';
    const FRAUD_TYPE_MADE_WITH_COUNTERFEIT_CARD = 'made_with_counterfeit_card';
    const FRAUD_TYPE_MADE_WITH_LOST_CARD = 'made_with_lost_card';
    const FRAUD_TYPE_MADE_WITH_STOLEN_CARD = 'made_with_stolen_card';
    const FRAUD_TYPE_MISC = 'misc';
    const FRAUD_TYPE_UNAUTHORIZED_USE_OF_CARD = 'unauthorized_use_of_card';

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
