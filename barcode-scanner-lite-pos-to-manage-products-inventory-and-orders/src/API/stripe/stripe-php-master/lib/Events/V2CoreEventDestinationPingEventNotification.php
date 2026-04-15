<?php


namespace Stripe\Events;

class V2CoreEventDestinationPingEventNotification extends \Stripe\V2\Core\EventNotification
{
    const LOOKUP_TYPE = 'v2.core.event_destination.ping';
    public $related_object;

    public function fetchEvent()
    {
        return parent::fetchEvent();
    }

    public function fetchRelatedObject()
    {
        return parent::fetchRelatedObject();
    }
}
