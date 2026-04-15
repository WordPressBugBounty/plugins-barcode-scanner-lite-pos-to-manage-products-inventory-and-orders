<?php


namespace Stripe\V2\Billing;

class MeterEventAdjustment extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'v2.billing.meter_event_adjustment';

    const STATUS_COMPLETE = 'complete';
    const STATUS_PENDING = 'pending';
}
