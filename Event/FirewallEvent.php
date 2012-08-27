<?php

namespace AC\Component\Firewall\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Basic firewall event.
 *
 * @package Firewall
 * @author Evan Villemez
 */
class FirewallEvent extends Event
{
    private $response;
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
        $this->stopPropagation();
    }

    public function getResponse()
    {
        return $this->response;
    }
}
