<?php


namespace Stripe\Service;

class CreditNoteService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/credit_notes', $params, $opts);
    }

    public function allLines($parentId, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/v1/credit_notes/%s/lines', $parentId), $params, $opts);
    }

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/v1/credit_notes', $params, $opts);
    }

    public function preview($params = null, $opts = null)
    {
        return $this->request('get', '/v1/credit_notes/preview', $params, $opts);
    }

    public function previewLines($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/credit_notes/preview/lines', $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/credit_notes/%s', $id), $params, $opts);
    }

    public function update($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/credit_notes/%s', $id), $params, $opts);
    }

    public function voidCreditNote($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/credit_notes/%s/void', $id), $params, $opts);
    }
}
