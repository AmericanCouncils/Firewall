<?php

namespace AC\Component\Firewall\Event;

class FirewallEvent
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