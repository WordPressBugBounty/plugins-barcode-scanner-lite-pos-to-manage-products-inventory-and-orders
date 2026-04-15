<?php


namespace Stripe\Service;

class TaxCodeService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/tax_codes', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/tax_codes/%s', $id), $params, $opts);
    }
}
