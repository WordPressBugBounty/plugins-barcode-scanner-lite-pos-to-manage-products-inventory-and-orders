<?php


namespace Stripe\Service;

class InvoiceRenderingTemplateService extends AbstractService
{
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/invoice_rendering_templates', $params, $opts);
    }

    public function archive($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoice_rendering_templates/%s/archive', $id), $params, $opts);
    }

    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/invoice_rendering_templates/%s', $id), $params, $opts);
    }

    public function unarchive($id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/v1/invoice_rendering_templates/%s/unarchive', $id), $params, $opts);
    }
}
