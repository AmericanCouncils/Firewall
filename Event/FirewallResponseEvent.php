<?php

namespace AC\Component\Firewall\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FirewallResponseEvent extends FirewallEvent
{
	private $response;

	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request);
		$this->response = $response;
	}
	
	public function getResponse()
	{
		return $this->response;
	}
    
    public function setResponse(Response $response)
    {
        throw new \LogicException("Cannot set responses for this event, a response has already been set.");
    }
}