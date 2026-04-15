<?php


namespace Stripe\Service;

class CustomerSessionService extends AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/customer_sessions', $params, $opts);
    }
}
