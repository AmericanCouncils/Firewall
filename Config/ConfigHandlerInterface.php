<?php

namespace AC\Component\Firewall\Config;

use AC\Component\Firewall\Firewall;
use AC\Component\Firewall\Event\ConfigureFirewallEvent;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Firewall $firewall 
     * @param Request $request 
     * @param string $config 
     * @return void
     * @author Evan Villemez
     */
    public function onFirewallConfigure(ConfigureFirewallEvent $event, $config);
}