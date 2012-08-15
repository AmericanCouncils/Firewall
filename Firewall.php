<?php

namespace AC\Component\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
                return $response;
            }

            //verify request event
            if ($response = $this->dispatcher->dispatch(FirewallEvents::REQUEST, new FirewallEvent($request))->getResponse()) {
                return $response;
            }

        } catch (\Exception $e) {

            //fire exception event
            if ($response = $this->dispatcher->dispatch(FirewallEvents::EXCEPTION, new FirewallExceptionEvent($request, $e))->getResponse()) {
                return $response;
            }
            
            //throw the original exception if we didn't get a response from any of the firewall listeners
            throw $e;
        }
        
        //success event
        if ($response = $this->dispatcher->dispatch(FirewallEvents::SUCCESS, new FirewallEvent($request))->getResponse()) {
            return $response;
        }
        
        return true;
    }
    
    /**
     * @see Symfony\Component\EventDispatcher\EventDispatcherInterface::addListener
     */
    public function addListener($listener)
    {
        $this->dispatcher->addListener($listener);
    }
    
    /**
     * @see Symfony\Component\EventDispatcher\EventDispatcherInterface::addSubscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);
    }
    
}