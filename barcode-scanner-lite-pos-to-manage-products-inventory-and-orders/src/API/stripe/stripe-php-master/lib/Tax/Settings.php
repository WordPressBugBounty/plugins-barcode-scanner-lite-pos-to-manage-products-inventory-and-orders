<?php


namespace Stripe\Tax;

class Settings extends \Stripe\SingletonApiResource
{
    const OBJECT_NAME = 'tax.settings';

    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';

    public static function retrieve($opts = null)
    {
        $opts = \Stripe\Util\RequestOptions::parse($opts);
        $instance = new static(null, $opts);
        $instance->refresh();

        return $instance;
    }

    public static function update($params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = '/v1/tax/settings';

        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    public function save($opts = null)
    {
        $params = $this->serializeParameters();
        if (\count($params) > 0) {
            $url = $this->instanceUrl();
            list($response, $opts) = $this->_request('post', $url, $params, $opts, ['save']);
            $this->refreshFrom($response, $opts);
        }

        return $this;
    }
}
