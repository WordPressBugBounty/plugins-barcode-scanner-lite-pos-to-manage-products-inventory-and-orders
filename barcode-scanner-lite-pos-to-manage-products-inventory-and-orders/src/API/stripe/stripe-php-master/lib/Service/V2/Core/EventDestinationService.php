<?php


namespace Stripe\Service\V2\Core;

class EventDestinationService extends \Stripe\Service\AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v2/core/event_destinations', $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v2/core/event_destinations', $params, $opts);
    }

    public function delete($id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v2/core/event_destinations/%s', $id), $params, $opts);
    }

    public function disable($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v2/core/event_destinations/%s/disable', $id), $params, $opts);
    }

    public function enable($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v2/core/event_destinations/%s/enable', $id), $params, $opts);
    }

    public function ping($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v2/core/event_destinations/%s/ping', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v2/core/event_destinations/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v2/core/event_destinations/%s', $id), $params, $opts);
    }
}
