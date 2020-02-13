<?php
/**
 * Base User Gateway class
 */
namespace Omnipay\Common;

/**
 * Base user gateway class
 *
 * This abstract class should be extended by all user gatways
 * throughout the Omnipay system.  It enforces implementation of
 * the GatewayInterface interface and defines a few common attributes
 * and methods that all gateways should have.
 *
 * Example:
 *
 * <code>
 *     // Initialize the gateway
 *     $gateway->initialize(...);
 *
 *     // Get the gateway parameters
 *     $parameters = $gateway->getParameters();
 *
 *     // lookup a user by access token
 *     if ($gateway->supportsLookUp()) {
 *         $request = $gateway->lookUp();
 *         $response = $request->send();
 *     }
 * </code>
 */
abstract class AbstractUserGateway extends AbstractGenericGateway
{
    /**
     * Supports LookUp
     *
     * @return boolean Tru if this gateway has the lookUp() method
     */
    public function supportsFind()
    {
        return method_exists($this, 'find');
    }

    /**
     * Supports modify
     *
     * @return boolean True if this gateway has the modify() method
     */
    public function supportsModify()
    {
        return method_exists($this, 'modify');
    }

    /**
     * Supports register
     *
     * @return boolean True if this gateway has the register() method
     */
    public function supportsRegister()
    {
        return method_exists($this, 'register');
    }
}
