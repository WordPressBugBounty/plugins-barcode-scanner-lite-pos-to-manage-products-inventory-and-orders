<?php


namespace Stripe\Service;

class TaxRateService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/tax_rates', $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/tax_rates', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/tax_rates/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/tax_rates/%s', $id), $params, $opts);
    }
}
