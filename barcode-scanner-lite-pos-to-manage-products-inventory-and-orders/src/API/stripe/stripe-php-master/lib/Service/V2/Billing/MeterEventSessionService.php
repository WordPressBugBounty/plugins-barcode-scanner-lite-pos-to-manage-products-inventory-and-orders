<?php


namespace Stripe\Service\V2\Billing;

class MeterEventSessionService extends \Stripe\Service\AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v2/billing/meter_event_session', $params, $opts);
    }
}
