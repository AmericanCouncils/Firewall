<?php

namespace AC\Component\Firewall\Listener;

use AC\Component\Firewall\Event;

class IpWhitelist
{
	protected $patterns = array();
	
	public function __construct(array $patterns)
	{
        $this->patterns = $patterns;
	}
	
	public function onFirewallRequest(FirewallEvent $e)
    {
        $ip = $e->getRequest()->getClientIp();
        
        $failed = true;
        foreach ($this->patterns as $pattern) {
            //if pass $failed = false
        }
        
        if ($failed) {
            throw new \RuntimeException("Access is denied from your location.");
        }
        
        return true;
    }

}