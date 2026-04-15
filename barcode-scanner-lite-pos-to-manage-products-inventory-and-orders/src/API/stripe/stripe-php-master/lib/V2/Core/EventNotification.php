<?php

namespace Stripe\V2\Core;

use Stripe\Events\UnknownEventNotification;
use Stripe\Reason;
use Stripe\RelatedObject;
use Stripe\Util\EventNotificationTypes;

abstract class EventNotification
{
    public $id;
    public $type;
    public $created;
    public $context;
    public $reason;
    public $livemode;

    protected $client;
    protected $related_object;

    public function __construct($json, $client)
    {
        $this->client = $client;

        if (\array_key_exists('id', $json)) {
            $this->id = $json['id'];
        }
        if (\array_key_exists('type', $json)) {
            $this->type = $json['type'];
        }
        if (\array_key_exists('created', $json)) {
            $this->created = $json['created'];
        }
        if (\array_key_exists('context', $json) && null !== $json['context']) {
            $this->context = \Stripe\StripeContext::parse($json['context']);
        }
        if (\array_key_exists('livemode', $json)) {
            $this->livemode = $json['livemode'];
        }
        if (\array_key_exists('related_object', $json)) {
            $this->related_object = new RelatedObject($json['related_object']);
        }
        if (\array_key_exists('reason', $json)) {
            $this->reason = new Reason($json['reason']);
        }
    }

    public static function fromJson($jsonStr, $client)
    {
        $json = json_decode($jsonStr, true);

        $class = UnknownEventNotification::class;
        $eventNotificationTypes = EventNotificationTypes::v2EventMapping;
        if (\array_key_exists($json['type'], $eventNotificationTypes)) {
            $class = $eventNotificationTypes[$json['type']];
        }

        return new $class($json, $client);
    }

    public function fetchEvent()
    {
        $response = $this->client->rawRequest(
            'get',
            "/v2/core/events/{$this->id}",
            null,
            ['stripe_context' => $this->context],
            null,
            ['fetch_event']
        );

        return $this->client->deserialize($response->body, 'v2');
    }

    protected function fetchRelatedObject()
    {
        if (null === $this->related_object) {
            return null;
        }

        $options = [];
        if (null !== $this->context) {
            $options['stripe_context'] = $this->context;
        }

        $response = $this->client->rawRequest(
            'get',
            $this->related_object->url,
            null,
            $options,
            null,
            ['fetch_related_object']
        );

        return $this->client->deserialize($response->body, \Stripe\Util\Util::getApiMode($this->related_object->url));
    }
}
