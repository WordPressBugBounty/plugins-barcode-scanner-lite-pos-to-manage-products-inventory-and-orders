<?php

namespace Stripe\Events;

use Stripe\V2\Core\EventNotification;

class UnknownEventNotification extends EventNotification
{
    public $related_object;

    public function fetchRelatedObject()
    {
        return parent::fetchRelatedObject();
    }
}
