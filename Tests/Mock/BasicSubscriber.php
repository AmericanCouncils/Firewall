<?php
namespace AC\Component\Firewall\Tests\Mock;

use AC\Component\Firewall\Event\FirewallEvent;
use AC\Component\Firewall\Event\FirewallEvents;
use AC\Component\Firewall\Event\ConfigureFirewallEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BasicSubscriber implements EventSubscriberInterface
{
    private $configure = false;
    private $request = false;
    private $success = false;
    
    public static function getSubscribedEvents()
    {
        return array(
            FirewallEvents::CONFIGURE => 'onFirewallConfigure',
            FirewallEvents::REQUEST => 'onFirewallRequest',
            FirewallEvents::SUCCESS => 'onFirewallSuccess',
        );
    }
    
    public function onFirewallConfigure(ConfigureFirewallEvent $e)
    {
        $this->configure = true;
    }
    
    public function onFirewallRequest(FirewallEvent $e)
    {
        $this->request = true;
    }
    
    public function onFirewallSuccess(FirewallEvent $e)
    {
        $this->success = true;
    }
    
    public function handledConfigure()
    {
        return $this->configure;
    }
    
    public function handledRequest()
    {
        return $this->request;
    }
    
    public function handledSuccess()
    {
        return $this->success;
    }
}