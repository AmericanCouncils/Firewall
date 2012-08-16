<?php

namespace AC\Component\Firewall\Tests\Mock;


use AC\Component\Firewall\Config\ConfigHandlerInterface;
use AC\Component\Firewall\Event\ConfigureFirewallEvent;
use AC\Component\Firewall\Event\FirewallEvents;

class ConfigHandler implements ConfigHandlerInterface
{
    protected $testConfig = false;
    
    public function getKey()
    {
        return 'test_handler';
    }
    
	public function onFirewallConfigure(ConfigureFirewallEvent $event, $config)
	{
		$this->testConfig = $config;
	}
    
    public function getTestConfig()
    {
        return $this->testConfig;
    }
}