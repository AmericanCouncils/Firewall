# Firewall #

This component provides a shell for an authentication system that can be used for validating any incoming `Request` instance.  All you need to
do to use it is to configure the `Firewall` instance however you like, and tell it to verify a `Request` object with `Firewall::verifyRequest`.

The Firewall works by providing it's own series of events that you can hook into with your own code in order to implement whatever types of 
authentication proceedures you need.

If the firewall succeeds in verifying the request, it returns true.  If there is a failure, it may return a response instance, or an exception will be thrown.

## Usage ##

Here is the most basic usage of the firewall for verifying incoming requests based on an IP blacklist and/or whitelist.  It takes a bit of set up, it's most useful if used with a framework that provides some solution for dependency injection.

    <?php
    use AC\Component\Firewall\Firewall;
    use AC\Component\Firewall\Event\FirewallEvents;
    use AC\Component\Firewall\Listener\IpBlacklist;
    use AC\Component\Firewall\Listener\IpWhitelist;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    $firewall = new Firewall();

    $ipWhitelist = new IpWhitelist(array('192.168.100.*', '10.0.*.*'));
    $firewall->addListener(FirewallEvents::REQUEST, array($whitelist, 'onFirewallRequest'));
    
    $ipBlacklist = new IpBlacklist(array('10.0.100.*'));
    $firewall->addListener(FirewallEvents::REQUEST, array($ipBlacklist, 'onFirewallRequest'));


    //if it fails, exceptions will be thrown to be handled by your application, or a Firewall listener may
    //handle a failure internally by returning a response
    $result = $firewall->verifyRequest(Request::createFromGlobals());

    if ($result instanceof Response) {
        $result->send();
    }

The configuration above would only allow requests through that are part of a local network, but still deny requests from 
client addresses that match `10.0.100.*`.  It will apply these checks on all requests.

## Configuration subscriber ##

The Firewall provides a flexible configuration subscriber which uses config handlers to dynamically register firewall listeners based
on the incoming request, and request-specific configuration.
    
    <?php
    use AC\Component\Firewall\Firewall;
    use AC\Component\Firewall\Config\ConfigSubscriber;
    use AC\Component\Firewall\Config\IpBlacklistHandler;
    use AC\Component\Firewall\Config\IpWhitelistHandler;
    use Symfony\Component\HttpFoundation\Request;

    //define dynamic firewall config by assigning handler keys + config to a path regex
    $firewallRules = array(
        '^/admin' => array(
            'ip_blacklist' => array('192.168.100.*', '10.0.*.*'),
            'ip_whitelist' => array('10.0.100.*'),
        ),
    );
    
    //register factory handlers
    $subscriber = new ConfigSubscriber($firewallRules);
    $subscriber->addConfigHandler(new IpBlacklistHandler());
    $subscriber->addConfigHandler(new IpWhitelistHandler());
    
    //instantiate firewall with listener config
    $firewall = new Firewall();
    $firewall->addSubscriber($subscriber);
    
    //verify the request
    $result = $firewall->verifyRequest(Request::createFromGlobals());
    
This example applies specific configuration to certain configuration handlers, but only if the request path matches any of the rules.  In this case, only requests from an internal network would be allowed to access any route beginning with `/admin`.

## Events ##

The firewall fires a series of events for your any custom authentication systems to hook into.  The events are documented in the `AC\Component\Event\FirewallEvents` class.  You can register events or event subscribers on the firewall the same as you would any other instance of an `EventDispatcher`.  

