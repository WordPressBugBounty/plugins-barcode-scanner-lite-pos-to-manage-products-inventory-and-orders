<?php


namespace Stripe\Service\V2\Core;

class AccountService extends \Stripe\Service\AbstractService
{
    use \Stripe\Service\ServiceNavigatorTrait;

    protected static $classMap = [
        'persons' => Accounts\PersonService::class,
        'personTokens' => Accounts\PersonTokenService::class,
    ];

    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v2/core/accounts', $params, $opts);
    }

    public function close($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v2/core/accounts/%s/close', $id), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v2/core/accounts', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v2/core/accounts/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v2/core/accounts/%s', $id), $params, $opts);
    }

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
