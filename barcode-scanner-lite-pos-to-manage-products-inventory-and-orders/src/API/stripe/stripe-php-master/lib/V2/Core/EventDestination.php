<?php


namespace Stripe\V2\Core;

class EventDestination extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'v2.core.event_destination';

    const EVENT_PAYLOAD_SNAPSHOT = 'snapshot';
    const EVENT_PAYLOAD_THIN = 'thin';

    const STATUS_DISABLED = 'disabled';
    const STATUS_ENABLED = 'enabled';

    const TYPE_AMAZON_EVENTBRIDGE = 'amazon_eventbridge';
    const TYPE_WEBHOOK_ENDPOINT = 'webhook_endpoint';
}
