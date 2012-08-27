<?php

namespace AC\Component\Firewall\Event;

use Symfony\Component\HttpFoundation\Request;

/**
 * Fires when an exception is thrown from within the Firewall.
 * This gives Firewall listeners a chance to listen for security-related
 * exceptions and create a response accordingly
 *
 * @package Firewall
 * @author Evan Villemez
 */
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
