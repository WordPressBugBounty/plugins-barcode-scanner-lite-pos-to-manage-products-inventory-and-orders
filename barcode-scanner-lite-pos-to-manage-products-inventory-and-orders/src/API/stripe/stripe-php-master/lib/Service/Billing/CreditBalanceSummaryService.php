<?php


namespace Stripe\Service\Billing;

class CreditBalanceSummaryService extends \Stripe\Service\AbstractService
{
    public function retrieve($params = null, $opts = null)
    {
        return $this->request('get', '/v1/billing/credit_balance_summary', $params, $opts);
    }
}
