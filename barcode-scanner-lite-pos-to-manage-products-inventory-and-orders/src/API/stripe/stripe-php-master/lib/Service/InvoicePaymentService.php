<?php


namespace Stripe\Service;

class InvoicePaymentService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/invoice_payments', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/invoice_payments/%s', $id), $params, $opts);
    }
}
