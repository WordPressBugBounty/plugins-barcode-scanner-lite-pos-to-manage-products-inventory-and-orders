<?php


namespace Stripe\Service\Billing;

class AlertService extends \Stripe\Service\AbstractService
{
    public function activate($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/billing/alerts/%s/activate', $id), $params, $opts);
    }

    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/billing/alerts', $params, $opts);
    }

    public function archive($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/billing/alerts/%s/archive', $id), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/billing/alerts', $params, $opts);
    }

    public function deactivate($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/billing/alerts/%s/deactivate', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/billing/alerts/%s', $id), $params, $opts);
    }
}
