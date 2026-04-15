<?php


namespace Stripe\Service\Billing;

class MeterEventAdjustmentService extends \Stripe\Service\AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/billing/meter_event_adjustments', $params, $opts);
    }
}
