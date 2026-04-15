<?php


namespace Stripe\Service\V2\Core;

class AccountTokenService extends \Stripe\Service\AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v2/core/account_tokens', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v2/core/account_tokens/%s', $id), $params, $opts);
    }
}
