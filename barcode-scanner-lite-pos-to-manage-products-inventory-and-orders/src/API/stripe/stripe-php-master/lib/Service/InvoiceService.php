<?php


namespace Stripe\Service;

class InvoiceService extends AbstractService
{
    public function addLines($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoices/%s/add_lines', $id), $params, $opts);
    }

    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/invoices', $params, $opts);
    }

    public function allLines($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/invoices/%s/lines', $parentId), $params, $opts);
    }

    public function attachPayment($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoices/%s/attach_payment', $id), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/invoices', $params, $opts);
    }

    public function createPreview($params = null, $opts = null)
    {
        return $this->request('post', '/v1/invoices/create_preview', $params, $opts);
    }

    public function delete($id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/v1/invoices/%s', $id), $params, $opts);
    }

    public function finalizeInvoice($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoices/%s/finalize', $id), $params, $opts);
    }

    public function markUncollectible($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoices/%s/mark_uncollectible', $id), $params, $opts);
    }

    public function pay($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoices/%s/pay', $id), $params, $opts);
    }

    public function removeLines($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoices/%s/remove_lines', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/invoices/%s', $id), $params, $opts);
    }

    public function search($params = null, $opts = null)
    {
        return $this->requestSearchResult('get', '/v1/invoices/search', $params, $opts);
    }

    public function sendInvoice($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoices/%s/send', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoices/%s', $id), $params, $opts);
    }

    public function updateLine($parentId, $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoices/%s/lines/%s', $parentId, $id), $params, $opts);
    }

    public function updateLines($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoices/%s/update_lines', $id), $params, $opts);
    }

    public function voidInvoice($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoices/%s/void', $id), $params, $opts);
    }
}
