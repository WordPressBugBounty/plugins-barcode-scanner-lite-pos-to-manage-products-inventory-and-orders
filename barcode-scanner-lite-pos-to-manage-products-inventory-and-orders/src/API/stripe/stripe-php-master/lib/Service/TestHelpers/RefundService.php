<?php


namespace Stripe\Service\TestHelpers;

class RefundService extends \Stripe\Service\AbstractService
{
    public function expire($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/refunds/%s/expire', $id), $params, $opts);
    }
}
