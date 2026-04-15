<?php


namespace Stripe\Radar;

class ValueList extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'radar.value_list';

    use \Stripe\ApiOperations\Update;

    const ITEM_TYPE_CARD_BIN = 'card_bin';
    const ITEM_TYPE_CARD_FINGERPRINT = 'card_fingerprint';
    const ITEM_TYPE_CASE_SENSITIVE_STRING = 'case_sensitive_string';
    const ITEM_TYPE_COUNTRY = 'country';
    const ITEM_TYPE_CUSTOMER_ID = 'customer_id';
    const ITEM_TYPE_EMAIL = 'email';
    const ITEM_TYPE_IP_ADDRESS = 'ip_address';
    const ITEM_TYPE_SEPA_DEBIT_FINGERPRINT = 'sepa_debit_fingerprint';
    const ITEM_TYPE_STRING = 'string';
    const ITEM_TYPE_US_BANK_ACCOUNT_FINGERPRINT = 'us_bank_account_fingerprint';

    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    public function delete($params = null, $opts = null)
    {
        self::_validateParams($params);

        $url = $this->instanceUrl();
        list($response, $opts) = $this->_request('delete', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

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

    public static function update($id, $params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::resourceUrl($id);

        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }
}
