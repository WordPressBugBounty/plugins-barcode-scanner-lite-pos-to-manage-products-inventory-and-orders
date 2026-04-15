<?php


namespace Stripe;

class TaxRate extends ApiResource
{
    const OBJECT_NAME = 'tax_rate';

    use ApiOperations\Update;

    const JURISDICTION_LEVEL_CITY = 'city';
    const JURISDICTION_LEVEL_COUNTRY = 'country';
    const JURISDICTION_LEVEL_COUNTY = 'county';
    const JURISDICTION_LEVEL_DISTRICT = 'district';
    const JURISDICTION_LEVEL_MULTIPLE = 'multiple';
    const JURISDICTION_LEVEL_STATE = 'state';

    const RATE_TYPE_FLAT_AMOUNT = 'flat_amount';
    const RATE_TYPE_PERCENTAGE = 'percentage';

    const TAX_TYPE_AMUSEMENT_TAX = 'amusement_tax';
    const TAX_TYPE_COMMUNICATIONS_TAX = 'communications_tax';
    const TAX_TYPE_GST = 'gst';
    const TAX_TYPE_HST = 'hst';
    const TAX_TYPE_IGST = 'igst';
    const TAX_TYPE_JCT = 'jct';
    const TAX_TYPE_LEASE_TAX = 'lease_tax';
    const TAX_TYPE_PST = 'pst';
    const TAX_TYPE_QST = 'qst';
    const TAX_TYPE_RETAIL_DELIVERY_FEE = 'retail_delivery_fee';
    const TAX_TYPE_RST = 'rst';
    const TAX_TYPE_SALES_TAX = 'sales_tax';
    const TAX_TYPE_SERVICE_TAX = 'service_tax';
    const TAX_TYPE_VAT = 'vat';

    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, Collection::class, $params, $opts);
    }

    public static function retrieve($id, $opts = null)
    {
        $opts = Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }

    public static function update($id, $params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::resourceUrl($id);

        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }
}
