<?php

namespace AC\Component\Firewall\Silex;

use Silex\Application;
use Silex\SilexEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use AC\Component\Firewall\Config\ConfigSubscriber;
use AC\Component\Firewall\Config\IpBlacklistHandler;

/**
 * Service provider for Silex instances.
 *
 * @package Firewall
 * @author Evan Villemez
 */
class FirewallServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        //shared firewall service
        $app['firewall'] = $app->share(function($app) {
            $f = new Firewall($app['dispatcher']);

            $f->addSubscriber($app['firewall.configurator']);

            return $f;
        });

        //configuration service - can be used by other extensions
        //in Application::boot to register other config handlers
        $app['firewall.configurator'] = $app->share(function($c) {
            $s = new ConfigSubscriber($app->getParameter('firewall.rules', array()));

            $s->addConfigHandler(new IpBlacklistHandler);
            $s->addConfigHandler(new IpWhitelisttHandler);
        });
    }

    public function boot(Application $app)
    {
        //register firewall listener for request
        $app['dispatcher']->addListener(SilexEvents::BEFORE, function(GetResponseEvent $e) use ($app) {
            if ($response = $app['firewall']->verifyRequest($e->getRequest()) instanceof Response) {
                $e->setResponse($response);
            }

            return true;
        });

    }
}
