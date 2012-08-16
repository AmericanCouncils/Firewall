<?php

namespace AC\Component\Firewall;

use AC\Component\Firewall\Event\FirewallEvent;
use AC\Component\Firewall\Event\FirewallEvents;
use AC\Component\Firewall\Event\ConfigureFirewallEvent;
use AC\Component\Firewall\Event\FirewallResponseEvent;
use AC\Component\Firewall\Event\FirewallExceptionEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides the shell for an authentication system by using an EventDispatcher to dispatch firewall events.
 * Potentially many listeners have a chance to validate an incoming request, and handle any exceptions thrown
 * as a result.
 *
 * @package Firewall
 * @author Evan Villemez
 */
class Firewall
{
    protected $dispatcher;
    
    /**
     * Constructor requires an event dispatcher to dispatch firewall events.
     *
     * @param EventDispatcherInterface $dispatcher 
     */
	public function __construct(EventDispatcherInterface $dispatcher = null)
    {
        if(!$dispatcher) {
            $dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher;
        }
        $this->dispatcher = $dispatcher;
    }
    
    /**
     * Verify an incoming request by dispatching events to firewall listeners.
     *
     * @param Request $request 
     * @return true|Response  Will return true on success, could potentially return a response.
     * @throws Exception  Will throw any exceptions caught, if not handled by a listener.
     */
    public function verifyRequest(Request $request)
    {
        try {
            
            //config event, listeners may add listeners/subscribers for other firewall events depending on the request
            if ($response = $this->dispatcher->dispatch(FirewallEvents::CONFIGURE, new ConfigureFirewallEvent($this, $request))->getResponse()) {
                return $this->dispatcher->dispatch(FirewallEvents::RESPONSE, new FirewallResponseEvent($request, $response))->getResponse();
            }

            //verify request event
            if ($response = $this->dispatcher->dispatch(FirewallEvents::REQUEST, new FirewallEvent($request))->getResponse()) {
                return $this->dispatcher->dispatch(FirewallEvents::RESPONSE, new FirewallResponseEvent($request, $response))->getResponse();
            }

        } catch (\Exception $e) {

            //fire exception event
            if ($response = $this->dispatcher->dispatch(FirewallEvents::EXCEPTION, new FirewallExceptionEvent($request, $e))->getResponse()) {
                return $this->dispatcher->dispatch(FirewallEvents::RESPONSE, new FirewallResponseEvent($request, $response))->getResponse();
            }
            
            //throw the original exception if we didn't get a response from any of the firewall listeners
            throw $e;
        }
        
        //success event
        if ($response = $this->dispatcher->dispatch(FirewallEvents::SUCCESS, new FirewallEvent($request))->getResponse()) {
            return $this->dispatcher->dispatch(FirewallEvents::RESPONSE, new FirewallResponseEvent($request, $response))->getResponse();
        }
        
        return true;
    }
    
    /**
     * @see Symfony\Component\EventDispatcher\EventDispatcherInterface::addListener
     */
    public function addListener($eventName, $listener)
    {
        $this->dispatcher->addListener($eventName, $listener);
    }
    
    /**
     * @see Symfony\Component\EventDispatcher\EventDispatcherInterface::addSubscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);
    }
    
}