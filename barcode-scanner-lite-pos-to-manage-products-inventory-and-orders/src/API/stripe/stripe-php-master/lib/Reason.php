<?php

namespace Stripe;

class ReasonRequest
{
    public $id;
    public $idempotency_key;

    public function __construct($json)
    {
        $this->id = $json['id'];
        $this->idempotency_key = $json['idempotency_key'];
    }
}

class Reason
{
    public $type;
    public $request;

    public function __construct($json)
    {
        $this->type = $json['type'];

        if ('request' === $this->type) {
            $this->request = new ReasonRequest($json['request']);
        } else {
            $this->request = null;
        }
    }
}
