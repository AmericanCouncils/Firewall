<?php

namespace AC\Component\Firewall\Event;

/**
 * This class documents the events fired by the Firewall.  All event classes
 * dispatched have the option of setting a Response, which prevent the Firewall
 * from further processing anything.
 *
 * @package Firewall
 * @author Evan Villemez
 */
class FirewallEvents
{
    /**
     * Fires before the request verification event, in order to allow pre-configuration
     * based on the contents of the incoming request.
     *
     * Listeners receieve an instance of `ConfigureFirewallEvent`, and can
     * use that to register event listeners or event subscribers for the
     * other firewall events described below.
     */
    const CONFIGURE = 'firewall.configure';
    
    /**
     * Fires when the firewall receives a request, just after the configure event.
     *
     * Listeners receive an instance of `FirewallEvent`
     */
	const REQUEST = 'firewall.request';

    /**
     * This event fires in the event of an exception being thrown during the
     * verification process.
     *
     * Listeners receive an instance of `FirewallExceptionEvent`
     */
	const EXCEPTION = 'firewall.exception';

    /**
     * Fires after the request event if no exceptions were thrown.  It's assumed that
     * at this point verification of the request succeeded.
     *
     * Listeners receive an instance of `FirewallEvent`
     */
	const SUCCESS = 'firewall.success';
    
    /**
     * Fires when the firewall returns a response.  This may happen as the
     * result of an exception.
     *
     * Listeners receive an instance of `FirewallResponseEvent`
     */
    const RESPONSE = 'firewall.response';

}