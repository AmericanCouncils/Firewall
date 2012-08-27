<?php

namespace AC\Component\Firewall\Event;

use AC\Component\Firewall\Firewall;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * An event for configuring the firewall.  This is done by adding event listeners
 * or subscribers to be registered with the Firewall's EventDispatcher instance
 *
 * @package Firewall
 * @author Evan Villemez
 */
class ConfigureFirewallEvent extends FirewallEvent
{
    private $firewall;

    public function __construct(Firewall $firewall, Request $request)
    {
        $this->firewall = $firewall;
        parent::__construct($request);
    }

    /**
     * Add an event listener which will be registered in the Firewall's
     * EventDispatcher instance.
     *
     * @param mixed $listener
     */
    public function addFirewallListener($eventName, $listener)
    {
        $this->firewall->addListener($eventName, $listener);
    }

    /**
     * Add an event subscriber which will be registered in the Firewall's
     * EventDispatcher instance
     *
     * @param EventSubscriberInterface $subscriber
     */
    public function addFirewallSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->firewall->addSubscriber($subscriber);
    }
}
