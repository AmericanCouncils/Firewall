<?php

namespace AC\Component\Firewall\Tests;

use AC\Component\Firewall\Firewall;
use Symfony\Component\HttpFoundation\Request;

class FirewallTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $f = new Firewall;
        $this->assertNotNull($f);
        $this->assertTrue($f instanceof Firewall);
    }
    
    public function testVerifyRequest1()
    {
        $f = new Firewall;
        $this->assertTrue($f->verifyRequest(Request::create("/", "GET")));
    }
    
    public function testDispatchFirewallEvents()
    {
        $f = new Firewall;
        $s = new Mock\BasicSubscriber;
        
        $f->addSubscriber($s);
        $f->verifyRequest(Request::create("/", "GET"));
        
        $this->assertTrue($s->configNotified());
        $this->assertTrue($s->verifyNotified());
        $this->assertTrue($s->successNotified());
    }
    
    public function testNotifyException()
    {
        $f = new Firewall;
        $s = new Mock\ExceptionSubscriber;
        
        $f->addSubscriber($s);
        
        try {
            $f->verifyRequest(Request::create("/", "GET"));            
        } catch (\Exception $e) {
            $this->assertTrue($s->verifyNotified());
            $this->assertTrue($s->exceptionNotified());
            $this->assertTrue($e instanceof Mock\Exception);
        }
    }
}
