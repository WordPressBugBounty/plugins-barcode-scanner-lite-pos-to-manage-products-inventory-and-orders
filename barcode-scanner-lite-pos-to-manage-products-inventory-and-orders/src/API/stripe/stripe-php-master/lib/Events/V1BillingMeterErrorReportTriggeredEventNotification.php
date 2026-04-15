<?php


namespace Stripe\Events;

class V1BillingMeterErrorReportTriggeredEventNotification extends \Stripe\V2\Core\EventNotification
{
    const LOOKUP_TYPE = 'v1.billing.meter.error_report_triggered';
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
