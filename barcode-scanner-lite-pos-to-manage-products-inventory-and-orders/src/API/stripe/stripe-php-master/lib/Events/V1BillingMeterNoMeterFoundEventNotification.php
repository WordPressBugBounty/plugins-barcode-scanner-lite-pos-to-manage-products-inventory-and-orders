<?php


namespace Stripe\Events;

class V1BillingMeterNoMeterFoundEventNotification extends \Stripe\V2\Core\EventNotification
{
    const LOOKUP_TYPE = 'v1.billing.meter.no_meter_found';

    public function fetchEvent()
    {
        return parent::fetchEvent();
    }
}
