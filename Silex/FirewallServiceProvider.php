<?php

namespace AC\Component\Firewall\Silex;

use Silex\Application;

class FirewallServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        //shared firewall service
        $app['firewall'] = $app->share(function($c) {
            $f = new Firewall($app['dispatcher']);

            //setup config
            

            return $f;
        });
        
        //configuration service
        $app['firewall.configurator'] = $app->protect(function($c) {
        
        });
    }
    
    public function boot(Application $app)
    {
        //register firewall listener for request
        $app['dispatcher']->addListener(SilexEvents::BEFORE, function(GetResponseEvent $e) use ($app) {
            if ($response = $app['firewall']->verifyRequest($e->getRequest()) instanceof Response) {
                return $response;
            }
            
            return true;
        });
                
    }
}