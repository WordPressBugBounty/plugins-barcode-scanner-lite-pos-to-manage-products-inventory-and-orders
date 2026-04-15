<?php


namespace Stripe;

class EphemeralKey extends ApiResource
{
    const OBJECT_NAME = 'ephemeral_key';

    public function delete($params = null, $opts = null)
    {
        self::_validateParams($params);

        $url = $this->instanceUrl();
        list($response, $opts) = $this->_request('delete', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    use ApiOperations\Create {
        create as protected _create;
    }

    public static function create($params = null, $opts = null)
    {
        if (!$opts || !isset($opts['stripe_version'])) {
            throw new Exception\InvalidArgumentException('stripe_version must be specified to create an ephemeral key');
        }

        return self::_create($params, $opts);
    }
}
