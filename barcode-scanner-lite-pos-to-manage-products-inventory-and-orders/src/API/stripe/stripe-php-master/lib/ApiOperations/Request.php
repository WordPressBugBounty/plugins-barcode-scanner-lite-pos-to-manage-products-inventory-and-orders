<?php

namespace Stripe\ApiOperations;

trait Request
{
    protected static function _validateParams($params = null)
    {
        if ($params && !\is_array($params)) {
            $message = 'You must pass an array as the first argument to Stripe API '
                . 'method calls.  (HINT: an example call to create a charge '
                . "would be: \"Stripe\\Charge::create(['amount' => 100, "
                . "'currency' => 'usd', 'source' => 'tok_1234'])\")";

            throw new \Stripe\Exception\InvalidArgumentException($message);
        }
    }

    protected function _request($method, $url, $params = [], $options = null, $usage = [], $apiMode = 'v1')
    {
        $opts = $this->_opts->merge($options);
        list($resp, $options) = static::_staticRequest($method, $url, $params, $opts, $usage, $apiMode);
        $this->setLastResponse($resp);

        return [$resp->json, $options];
    }

    protected static function _requestPage($url, $resultClass, $params = null, $options = null, $usage = [])
    {
        self::_validateParams($params);

        list($response, $opts) = static::_staticRequest('get', $url, $params, $options, $usage);
        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $opts);
        if (!$obj instanceof $resultClass) {
            throw new \Stripe\Exception\UnexpectedValueException(
                'Expected type ' . $resultClass . ', got "' . \get_class($obj) . '" instead.'
            );
        }
        $obj->setLastResponse($response);
        $obj->setFilters($params);

        return $obj;
    }

    protected function _requestStream($method, $url, $readBodyChunk, $params = [], $options = null, $usage = [])
    {
        $opts = $this->_opts->merge($options);
        static::_staticStreamingRequest($method, $url, $readBodyChunk, $params, $opts, $usage);
    }

    protected static function _staticRequest($method, $url, $params, $options, $usage = [], $apiMode = 'v1')
    {
        $opts = \Stripe\Util\RequestOptions::parse($options);
        $baseUrl = isset($opts->apiBase) ? $opts->apiBase : static::baseUrl();
        $requestor = new \Stripe\ApiRequestor($opts->apiKey, $baseUrl);
        list($response, $opts->apiKey) = $requestor->request($method, $url, $params, $opts->headers, $apiMode, $usage);
        $opts->discardNonPersistentHeaders();

        return [$response, $opts];
    }

    protected static function _staticStreamingRequest($method, $url, $readBodyChunk, $params, $options, $usage = [])
    {
        $opts = \Stripe\Util\RequestOptions::parse($options);
        $baseUrl = isset($opts->apiBase) ? $opts->apiBase : static::baseUrl();
        $requestor = new \Stripe\ApiRequestor($opts->apiKey, $baseUrl);
        $requestor->requestStream($method, $url, $readBodyChunk, $params, $opts->headers);
    }
}
