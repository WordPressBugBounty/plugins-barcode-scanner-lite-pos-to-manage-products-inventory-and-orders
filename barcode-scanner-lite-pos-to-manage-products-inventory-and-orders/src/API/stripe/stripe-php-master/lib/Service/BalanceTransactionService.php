<?php


namespace Stripe\Service;

class BalanceTransactionService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/balance_transactions', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/balance_transactions/%s', $id), $params, $opts);
    }
}
