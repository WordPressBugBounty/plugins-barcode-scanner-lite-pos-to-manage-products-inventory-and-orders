<?php


namespace Stripe\Service;

class SubscriptionService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/subscriptions', $params, $opts);
    }

    public function cancel($id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/subscriptions/%s', $id), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/subscriptions', $params, $opts);
    }

    public function deleteDiscount($id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/subscriptions/%s/discount', $id), $params, $opts);
    }

    public function migrate($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/subscriptions/%s/migrate', $id), $params, $opts);
    }

    public function resume($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/subscriptions/%s/resume', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/subscriptions/%s', $id), $params, $opts);
    }

    public function search($params = null, $opts = null)
    {
        return $this->requestSearchResult('get', '/v1/subscriptions/search', $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/subscriptions/%s', $id), $params, $opts);
    }
}
