<?php


namespace Stripe\Service\TestHelpers\Terminal;

class ReaderService extends \Stripe\Service\AbstractService
{
    public function presentPaymentMethod($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/terminal/readers/%s/present_payment_method', $id), $params, $opts);
    }

    public function succeedInputCollection($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/terminal/readers/%s/succeed_input_collection', $id), $params, $opts);
    }

    public function timeoutInputCollection($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/test_helpers/terminal/readers/%s/timeout_input_collection', $id), $params, $opts);
    }
}
