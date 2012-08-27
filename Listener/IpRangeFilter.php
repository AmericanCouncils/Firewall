<?php

namespace AC\Component\Firewall\Listener;

use AC\Component\Firewall\Event\FirewallEvent;
use AC\Component\Firewall\Exception\InvalidIpException;

/**
 * Test IP addresses to check for whether or not they are allowed based on a whitelist or
 * blacklist of IP patterns and ranges.
 *
 * A pattern can beclared with wildcards:  192.168.100.*
 * And an explicit range can be declared with a dash: 192.168.100.32-192.168.100.64
 *
 * The filter will accept an array of wildcard patterns and ranges to check against.
 *
 * @package Firewall
 * @author Evan Villemez
 */
class IpRangeFilter
{
    /**
     * Sets the mode to whitelist, meaning if an ip is scanned and is NOT in a given
     * range, an exception will be thrown.
     */
    const WHITELIST = 1;

    /**
     * Sets the mode to blacklist (default), meaning if an ip IS within a given range
     * an exception will be thrown
     */
    const BLACKLIST = -1;

    /**
     * @var array Array of ip patterns/ranges received in constructor
     */
    protected $patterns = array();

    /**
     * Constructor can accept ip ranges/patterns and a mode, which defaults to blacklist
     *
     * @param array $patterns
     * @param int   $mode
     */
    public function __construct(array $patterns = null, $mode = self::BLACKLIST)
    {
        if (!in_array($mode, array(self::BLACKLIST, self::WHITELIST))) {
            throw new \InvalidArgumentException("Invalid mode set.");
        }

        $this->patterns = $patterns;
        $this->mode = $mode;
    }

    /**
     * Handle a firewall event for an incoming requests, check if the ip
     * is within a valid range, and handle according to mode.
     *
     * @param  FirewallEvent      $e
     * @throws InvalidIpException If the IP is not allowed based on received configuration.
     */
    public function onFirewallRequest(FirewallEvent $e)
    {
        $ip = $e->getRequest()->getClientIp();

        foreach ($this->patterns as $pattern) {
            if ($this->isIpInRange($ip, $pattern)) {

                //if the mode is blacklist, we can fail now because we got a match
                if (self::BLACKLIST === $this->mode) {
                    throw new InvalidIpException("Access is denied from your location.");
                } else {
                    //if we got a match, and this is a whitelist, then the request is allowed
                    //so we can return early
                    return;
                }
            }
        }

        //if we made it this far with no matches, and the mode is whitelist, then the
        //request is NOT allowed
        if (self::WHITELIST === $this->mode) {
            throw new InvalidIpException("Access is denied from your location.");
        }

        return true;
    }

    /**
     * Return boolean if IP is valid given a certain wildcard pattern or ip range
     *
     * @param  string  $ip
     * @param  string  $pattern
     * @return boolean
     */
    public function isIpInRange($ip, $pattern)
    {
        $ip = ip2long($ip);
        $range = (false === strpos($pattern, ":")) ? $this->getRangeIPv4($pattern) : $this->getRangeIPv6($pattern);

        return ($ip >= $range['start'] && $ip <= $range['end']);
    }

    protected function getRangeIPv4($pattern)
    {
        //check for explicit range first
        if (count($exp = explode("-", $pattern)) == 2) {
            return array(
                'start' => ip2long($exp[0]),
                'end' => ip2long($exp[1])
            );
        }

        //if no wildcards, it's a regular ip, so start/end are same
        if (false === strpos($pattern, "*")) {
            return array(
                'start' => ip2long($pattern),
                'end' => ip2long($pattern)
            );
        }

        //check wildcards
        $start = array();
        $end = array();
        foreach (explode(".", $pattern) as $section) {
            if ($section === '*') {
                $start[] = "0";
                $end[] = "255";
            } else {
                $start[] = $section;
                $end[] = $section;
            }
        }

        return array(
            'start' => ip2long(implode(".", $start)),
            'end' => ip2long(implode(".", $end))
        );
    }

    protected function getRangeIPv6($pattern)
    {
        throw new \RuntimeException(sprintf("%s not implemented.", __METHOD__));
    }

}
