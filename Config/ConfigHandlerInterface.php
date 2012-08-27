<?php

namespace AC\Component\Firewall\Config;

use AC\Component\Firewall\Firewall;
use AC\Component\Firewall\Event\ConfigureFirewallEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * Required interface for config handlers handled by the
 * ConfigSubscriber
 *
 * @package Firewall
 * @author Evan Villemez
 */
interface ConfigHandlerInterface
{
    /**
     * Return the string key for the type of config this instance handles
     *
     * @return string
     */
    public function getKey();

    /**
     * Configure a firewall by adding listeners/subscribers, based on the incoming request and received configuration.
     *
     * @param ConfigureFirewallEvent $event
     * @param mixed                  $config
     */
    public function onFirewallConfigure(ConfigureFirewallEvent $event, $config);
}
