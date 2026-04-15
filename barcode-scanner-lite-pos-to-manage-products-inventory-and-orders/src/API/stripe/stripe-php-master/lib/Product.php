<?php


namespace Stripe;

class Product extends ApiResource
{
    const OBJECT_NAME = 'product';

    use ApiOperations\NestedResource;
    use ApiOperations\Update;

    const TYPE_GOOD = 'good';
    const TYPE_SERVICE = 'service';

    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = Util\Util::convertToStripeObject($response->json, $opts);
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

    public static function search($params = null, $opts = null)
    {
        $url = '/v1/products/search';

        return static::_requestPage($url, SearchResult::class, $params, $opts);
    }

    const PATH_FEATURES = '/features';

    public static function allFeatures($id, $params = null, $opts = null)
    {
        return self::_allNestedResources($id, static::PATH_FEATURES, $params, $opts);
    }

    public static function createFeature($id, $params = null, $opts = null)
    {
        return self::_createNestedResource($id, static::PATH_FEATURES, $params, $opts);
    }

    public static function deleteFeature($id, $featureId, $params = null, $opts = null)
    {
        return self::_deleteNestedResource($id, static::PATH_FEATURES, $featureId, $params, $opts);
    }

    public static function retrieveFeature($id, $featureId, $params = null, $opts = null)
    {
        return self::_retrieveNestedResource($id, static::PATH_FEATURES, $featureId, $params, $opts);
    }
}
