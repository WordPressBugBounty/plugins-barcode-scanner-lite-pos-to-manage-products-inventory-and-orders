<?php


namespace Stripe\FinancialConnections;

class Account extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'financial_connections.account';

    const CATEGORY_CASH = 'cash';
    const CATEGORY_CREDIT = 'credit';
    const CATEGORY_INVESTMENT = 'investment';
    const CATEGORY_OTHER = 'other';

    const STATUS_ACTIVE = 'active';
    const STATUS_DISCONNECTED = 'disconnected';
    const STATUS_INACTIVE = 'inactive';

    const SUBCATEGORY_CHECKING = 'checking';
    const SUBCATEGORY_CREDIT_CARD = 'credit_card';
    const SUBCATEGORY_LINE_OF_CREDIT = 'line_of_credit';
    const SUBCATEGORY_MORTGAGE = 'mortgage';
    const SUBCATEGORY_OTHER = 'other';
    const SUBCATEGORY_SAVINGS = 'savings';

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

    public function disconnect($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/disconnect';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public static function allOwners($id, $params = null, $opts = null)
    {
        $url = static::resourceUrl($id) . '/owners';
        list($response, $opts) = static::_staticRequest('get', $url, $params, $opts);
        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    public function refreshAccount($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/refresh';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function subscribe($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/subscribe';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    public function unsubscribe($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/unsubscribe';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
