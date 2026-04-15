<?php


namespace Stripe\Service;

class SourceService extends AbstractService
{
    public function allSourceTransactions($id, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/sources/%s/source_transactions', $id), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/sources', $params, $opts);
    }

    public function detach($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/customers/%s/sources/%s', $parentId, $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/sources/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/sources/%s', $id), $params, $opts);
    }

    public function verify($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/sources/%s/verify', $id), $params, $opts);
    }
}
