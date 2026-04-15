<?php


namespace Stripe\Service;

class BalanceService extends AbstractService
{
    public function retrieve($params = null, $opts = null)
    {
        return $this->request('get', '/v1/balance', $params, $opts);
    }
}
