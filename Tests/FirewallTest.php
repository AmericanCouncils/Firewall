<?php

namespace AC\Component\Firewall\Tests;

use AC\Component\Firewall\Firewall;

class FirewallTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $f = new Firewall;
        $this->assertNotNull($f);
        $this->assertTrue($f instanceof Firewall);
    }
    
}