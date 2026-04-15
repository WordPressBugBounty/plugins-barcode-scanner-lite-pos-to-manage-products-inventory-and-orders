<?php


namespace Stripe\Service;

class SubscriptionScheduleService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/subscription_schedules', $params, $opts);
    }

    public function cancel($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/subscription_schedules/%s/cancel', $id), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/subscription_schedules', $params, $opts);
    }

    public function release($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/subscription_schedules/%s/release', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/subscription_schedules/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/subscription_schedules/%s', $id), $params, $opts);
    }
}
