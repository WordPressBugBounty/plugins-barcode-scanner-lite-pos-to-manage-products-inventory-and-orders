<?php


namespace Stripe\Service;

class PriceService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/prices', $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/prices', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/prices/%s', $id), $params, $opts);
    }

    public function search($params = null, $opts = null)
    {
        return $this->requestSearchResult('get', '/v1/prices/search', $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/prices/%s', $id), $params, $opts);
    }
}
