<?php
/**
 * Base Account Gateway class
 */
namespace Omnipay\Common;

/**
 * Base user gateway class
 *
 * This abstract class should be extended by all account gatways
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
 *     if ($gateway->supportsFind()) {
 *
 *     }
 * </code>
 */
abstract class AbstractAccountGateway extends AbstractGateway
{
    /**
     * Supports Find
     *
     * @return string
     */
    public function supportsFind()
    {
        return method_exists($this, 'find');
    }

    /**
     * Supports create
     *
     * @return  string
     */
    public function supportsCreate()
    {
        return method_exists($this, 'create');
    }

    /**
     * Supports Modify
     *
     * @return string
     */
    public function supportsModify()
    {
        return method_exists($this, 'modify');
    }

    /**
     * Supports Delete
     *
     * @return string
     */
    public function supportsDelete()
    {
        return method_exists($this, 'delete');
    }
}
