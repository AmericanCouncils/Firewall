<?php

namespace AC\Component\Firewall\Tests\Mock;

use Symfony\Component\HttpFoundation\Response;
use AC\Component\Firewall\Event\FirewallEvent;
use AC\Component\Firewall\Event\FirewallEvents;
use AC\Component\Firewall\Event\FirewallResponseEvent;
use AC\Component\Firewall\Event\FirewallExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $request = false;
    private $exception = false;
    private $response = false;

    public static function getSubscribedEvents()
    {
        return array(
            FirewallEvents::REQUEST => 'onFirewallRequest',
            FirewallEvents::EXCEPTION => 'onFirewallException',
            FirewallEvents::RESPONSE => 'onFirewallResponse',
        );
    }

    public function onFirewallRequest(FirewallEvent $e)
    {
        $this->request = true;

        throw new Exception;
    }

    public function onFirewallException(FirewallExceptionEvent $e)
    {
        $this->exception = true;
        $e->setResponse(new Response("foo"));
    }

    public function onFirewallResponse(FirewallResponseEvent $e)
    {
        $this->response = true;
    }

    public function handledRequest()
    {
        return $this->request;
    }

    public function handledException()
    {
        return $this->exception;
    }

    public function handledResponse()
    {
        return $this->response;
    }
}
