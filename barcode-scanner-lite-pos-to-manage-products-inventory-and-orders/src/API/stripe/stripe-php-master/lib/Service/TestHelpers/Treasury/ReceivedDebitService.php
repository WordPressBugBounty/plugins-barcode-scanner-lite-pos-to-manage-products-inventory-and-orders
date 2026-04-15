<?php


namespace Stripe\Service\TestHelpers\Treasury;

class ReceivedDebitService extends \Stripe\Service\AbstractService
{
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/test_helpers/treasury/received_debits', $params, $opts);
    }
}
