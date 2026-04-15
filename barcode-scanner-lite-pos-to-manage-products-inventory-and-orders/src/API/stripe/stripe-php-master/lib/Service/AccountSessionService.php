<?php


namespace Stripe\Service;

class AccountSessionService extends AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/account_sessions', $params, $opts);
    }
}
