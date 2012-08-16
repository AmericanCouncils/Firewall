<?php

namespace AC\Component\Firewall\Tests;

use AC\Component\Firewall\Firewall;
use AC\Component\Firewall\Config\ConfigSubscriber;
use AC\Component\Firewall\Config\IpBlacklistHandler;
use AC\Component\Firewall\Config\IpWhitelistHandler;
use Symfony\Component\HttpFoundation\Request;

class ConfigSubscriberTest extends \PHPUnit_Framework_TestCase
{
    protected function createRequestFromIp($ip)
    {
        return Request::create("/", "GET", array(), array(), array(), array("REMOTE_ADDR" => $ip));
    }
    
    public function testInstantiate()
    {
        $s = new ConfigSubscriber();
        $this->assertNotNull($s);
        $this->assertTrue($s instanceof ConfigSubscriber);
    }
    
    public function testConfigHandler()
    {
        $testConfig = array('foo','bar');
        $s = new ConfigSubscriber(array(
            "^/" => array(
                "test_handler" => $testConfig,                
            )
        ));
        $h = new Mock\ConfigHandler;
        $s->addConfigHandler($h);
        
        $f = new Firewall;
        $f->addSubscriber($s);
        $this->assertTrue($f->verifyRequest($this->createRequestFromIp('127.0.0.1')));
        
        $this->assertSame($testConfig, $h->getTestConfig());
    }
    
    public function testInstantiateWithBlacklistRules()
    {
        $s = new ConfigSubscriber(array(
            "^/" => array(
                'ip_blacklist' => array('192.168.*.*'),
            )
        ));
        $s->addConfigHandler(new IpBlacklistHandler);
        $f = new Firewall;
        $f->addSubscriber($s);
        $this->assertTrue($f->verifyRequest($this->createRequestFromIp("80.0.0.0")));

        $s = new ConfigSubscriber(array(
            "^/" => array(
                'ip_blacklist' => array('192.168.*.*'),
            )
        ));
        $s->addConfigHandler(new IpBlacklistHandler);
        $f = new Firewall;
        $f->addSubscriber($s);
        
        $this->setExpectedException("AC\Component\Firewall\Exception\InvalidIpException");
        $f->verifyRequest($this->createRequestFromIp("192.168.30.40"));
    }
    
    public function testInstantiateWithWhitelistRules()
    {
        $s = new ConfigSubscriber(array(
            "^/" => array(
                'ip_whitelist' => array('192.168.*.*'),
            )
        ));
        $s->addConfigHandler(new IpWhitelistHandler);
        $f = new Firewall;
        $f->addSubscriber($s);
        $this->assertTrue($f->verifyRequest($this->createRequestFromIp("192.168.30.40")));

        $s = new ConfigSubscriber(array(
            "^/" => array(
                'ip_whitelist' => array('192.168.*.*'),
            )
        ));
        $s->addConfigHandler(new IpWhitelistHandler);
        $f = new Firewall;
        $f->addSubscriber($s);
        $this->setExpectedException("AC\Component\Firewall\Exception\InvalidIpException");
        $this->assertTrue($f->verifyRequest($this->createRequestFromIp("80.0.0.0")));
    }
    
    
}
