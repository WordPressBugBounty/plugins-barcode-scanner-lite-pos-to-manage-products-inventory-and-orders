<?php


namespace Stripe\Service;

class BalanceSettingsService extends AbstractService
{
    public function retrieve($params = null, $opts = null)
    {
        return $this->request('get', '/v1/balance_settings', $params, $opts);
    }

    public function update($params = null, $opts = null)
    {
        return $this->request('post', '/v1/balance_settings', $params, $opts);
    }
}
