<?php

namespace AC\Component\Firewall\Config;

use AC\Component\Firewall\Event\ConfigureFirewallEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher;

/**
 * This Firewall subscriber can be injected directly into the Firewall's constructor.  It allows registering
 * configuration to be applied to all incoming requests, based on matched request patterns.  Any matches
 * will lazily register Firewall listeners/subscribers if their rules apply to the incoming request, otherwise
 * they will be ignored.
 *
 * @package Firewall
 * @author Evan Villemez
 */
class ConfigSubscriber implements EventSubscriberInterface
{
    protected $rules;
    
    public function __construct($rules = array())
    {
        $this->rules = $rules;
    }

    /**
     * {@inheritdoc}
     */
	public static function getSubscribedEvents()
	{
		return array(
			FirewallEvents::CONFIGURE => array('onFirewallConfigure', -128),
		);
	}
    
    /**
     * Configure the firewall based on config mapped to route patterns.  Call handlers
     * to register firewall listeners if the patterns have any rules associated.
     *
     * @param ConfigureFirewallEvent $e 
     */    
    public function onFirewallConfigure(ConfigureFirewallEvent $e)
    {
        $request = $e->getRequest();
        
        //loop through rules, call config handlers if any rules match
        foreach ($this->rules as $pattern => $handlers) {
            $matcher = new RequestMatcher($pattern);
            if ($matcher->matches($request)) {
                foreach ($handlers as $handlerKey => $handlerConfig) {
                    
                    //if we have a handler for the key, call it
                    if (isset($this->handlers[$handlerKey])) {
                        $this->handlers[$handlerKey]->onFirewallConfigure($e, $handlerConfig);
                    }
                }
            }
        }
    }
    
    /**
     * Register a handler to handle a specific configuration key
     *
     * @param ConfigHandlerInterface $handler 
     */
    public function addConfigHandler(ConfigHandlerInterface $handler)
    {
        $this->handlers[$handler->getKey()] = $handler;
    }

}
