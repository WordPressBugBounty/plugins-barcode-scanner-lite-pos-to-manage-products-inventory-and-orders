<?php

namespace Stripe;

class RelatedObject
{
    public $id;
    public $type;
    public $url;

    public function __construct($json)
    {
        $this->id = $json['id'];
        $this->type = $json['type'];
        $this->url = $json['url'];
    }
}
