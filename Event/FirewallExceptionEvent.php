<?php

class FirewallExceptionEvent extends FirewallEvent
{
	protected $exception;
	
	public function __construct(Request $r, \Exception $e)
	{
		$this->exception = $e;
		parent::__construct($r);
	}
	
	public function getException() 
	{
		return $this->exception;
	}

}