<?php


namespace Stripe\Service;

class PlanService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/plans', $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/plans', $params, $opts);
    }

    public function delete($id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/plans/%s', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/plans/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/plans/%s', $id), $params, $opts);
    }
}
