<?php

namespace AC\Component\Firewall\Config;
use AC\Component\Firewall\Event\ConfigureFirewallEvent;
use AC\Component\Firewall\Event\FirewallEvents;
use AC\Component\Firewall\Listener\IpRangeFilter;

class IpBlacklistHandler implements ConfigHandlerInterface
{
	
	public function getKey()
	{
		return 'ip_blacklist';
	}
	
	public function onFirewallConfigure(ConfigureFirewallEvent $event, $config)
	{
		$event->addFirewallListener(FirewallEvents::REQUEST, array(new IpRangeFilter($config, IpRangeFilter::BLACKLIST), 'onFirewallRequest'));
	}
    
}