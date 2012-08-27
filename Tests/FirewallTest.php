<?php

namespace AC\Component\Firewall\Tests;

use AC\Component\Firewall\Firewall;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function testDispatchFirewallEvents1()
    {
        $f = new Firewall;
        $s = new Mock\BasicSubscriber;
        $this->assertFalse($s->handledConfigure());
        $this->assertFalse($s->handledRequest());
        $this->assertFalse($s->handledSuccess());

        $f->addSubscriber($s);
        $this->assertTrue($f->verifyRequest(Request::create("/", "GET")));

        $this->assertTrue($s->handledConfigure());
        $this->assertTrue($s->handledRequest());
        $this->assertTrue($s->handledSuccess());
    }

    public function testDispatchFirewallEvents2()
    {
        $f = new Firewall;
        $s = new Mock\ExceptionSubscriber;
        $this->assertFalse($s->handledRequest());
        $this->assertFalse($s->handledException());
        $this->assertFalse($s->handledResponse());

        $f->addSubscriber($s);

        $response = $f->verifyRequest(Request::create("/", "GET"));
        $this->assertTrue($s->handledRequest());
        $this->assertTrue($s->handledException());
        $this->assertTrue($s->handledResponse());
        $this->assertTrue($response instanceof Response);
        $this->assertSame("foo", $response->getContent());
    }
}
