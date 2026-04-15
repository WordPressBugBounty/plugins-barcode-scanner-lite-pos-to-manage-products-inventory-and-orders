<?php


namespace Stripe\Events;

class V2CoreEventDestinationPingEvent extends \Stripe\V2\Core\Event
{
    const LOOKUP_TYPE = 'v2.core.event_destination.ping';

    public function fetchRelatedObject()
    {
        $apiMode = \Stripe\Util\Util::getApiMode($this->related_object->url);
        list($object, $options) = $this->_request('get', $this->related_object->url, [], [
            'stripe_context' => $this->context,
        ], [], $apiMode);

        return \Stripe\Util\Util::convertToStripeObject($object, $options, $apiMode);
    }
}
