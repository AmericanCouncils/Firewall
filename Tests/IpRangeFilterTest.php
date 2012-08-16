<?php

namespace AC\Component\Firewall\Tests;

use AC\Component\Firewall\Listener\IpRangeFilter;

class IpRangeFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $f = new IpRangeFilter(array());
        $this->assertNotNull($f);
        $this->assertTrue($f instanceof IpRangeFilter);
    }
    
    public function testIsIpInRangeIPv4()
    {
        $f = new IpRangeFilter();
        
        $testRange = '192.168.100.12-192.168.100.22';
        $testIp = '192.168.100.11';
        $this->assertFalse($f->isIpInRange($testIp, $testRange));
        
        $testIp = '192.168.100.23';
        $this->assertFalse($f->isIpInRange($testIp, $testRange));

        $testIp = '192.168.100.14';
        $this->assertTrue($f->isIpInRange($testIp, $testRange));
    }
    
    public function testIsIpInPatternIPv4()
    {
        $f = new IpRangeFilter();
        $pattern = "192.168.*.*";

        $testIp = "192.164.32.45";        
        $this->assertFalse($f->isIpInRange($testIp, $pattern));

        $testIp = "192.168.32.45";        
        $this->assertTrue($f->isIpInRange($testIp, $pattern));
    }
    
    public function testIsIpEqualIPv4()
    {
        $f = new IpRangeFilter();
        $pattern = "192.168.0.1";
        $testIp = "192.168.0.1";
        
        $this->assertTrue($f->isIpInRange($testIp, $pattern));
    }
    
    public function testHandleFirewallRequestBlacklist()
    {
//        $this->assertTrue(false);
    }
    
    public function testHandleFirewallRequestWhitelist()
    {
//        $this->assertTrue(false);
    }
}
