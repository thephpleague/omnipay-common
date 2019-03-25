<?php
/**
 * Base payment gateway class
 */

namespace Omnipay\Common;

/**
 * Base payment gateway class
 *
 * This abstract class should be extended by all payment gateways
 * throughout the Omnipay system.  It enforces implementation of
 * the GatewayInterface interface and defines various common attibutes
 * and methods that all gateways should have.
 *
 * Example:
 *
 * <code>
 *   // Initialise the gateway
 *   $gateway->initialize(...);
 *
 *   // Get the gateway parameters.
 *   $parameters = $gateway->getParameters();
 *
 *   // Create a credit card object
 *   $card = new CreditCard(...);
 *
 *   // Do an authorisation transaction on the gateway
 *   if ($gateway->supportsAuthorize()) {
 *       $gateway->authorize(...);
 *   } else {
 *       throw new \Exception('Gateway does not support authorize()');
 *   }
 * </code>
 *
 * For further code examples see the *omnipay-example* repository on github.
 *
 */
abstract class AbstractGateway extends AbstractGenericGateway
{
    /**
     * @return string
     */
    public function getCurrency()
    {
        return strtoupper($this->getParameter('currency'));
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setCurrency($value)
    {
        return $this->setParameter('currency', $value);
    }

    /**
     * Supports Authorize
     *
     * @return boolean True if this gateway supports the authorize() method
     */
    public function supportsAuthorize()
    {
        return method_exists($this, 'authorize');
    }

    /**
     * Supports Complete Authorize
     *
     * @return boolean True if this gateway supports the completeAuthorize() method
     */
    public function supportsCompleteAuthorize()
    {
        return method_exists($this, 'completeAuthorize');
    }

    /**
     * Supports Capture
     *
     * @return boolean True if this gateway supports the capture() method
     */
    public function supportsCapture()
    {
        return method_exists($this, 'capture');
    }

    /**
     * Supports Purchase
     *
     * @return boolean True if this gateway supports the purchase() method
     */
    public function supportsPurchase()
    {
        return method_exists($this, 'purchase');
    }

    /**
     * Supports Complete Purchase
     *
     * @return boolean True if this gateway supports the completePurchase() method
     */
    public function supportsCompletePurchase()
    {
        return method_exists($this, 'completePurchase');
    }

    /**
     * Supports Fetch Transaction
     *
     * @return boolean True if this gateway supports the fetchTransaction() method
     */
    public function supportsFetchTransaction()
    {
        return method_exists($this, 'fetchTransaction');
    }

    /**
     * Supports Refund
     *
     * @return boolean True if this gateway supports the refund() method
     */
    public function supportsRefund()
    {
        return method_exists($this, 'refund');
    }

    /**
     * Supports Void
     *
     * @return boolean True if this gateway supports the void() method
     */
    public function supportsVoid()
    {
        return method_exists($this, 'void');
    }

    /**
     * Supports CreateCard
     *
     * @return boolean True if this gateway supports the create() method
     */
    public function supportsCreateCard()
    {
        return method_exists($this, 'createCard');
    }

    /**
     * Supports DeleteCard
     *
     * @return boolean True if this gateway supports the delete() method
     */
    public function supportsDeleteCard()
    {
        return method_exists($this, 'deleteCard');
    }

    /**
     * Supports UpdateCard
     *
     * @return boolean True if this gateway supports the update() method
     */
    public function supportsUpdateCard()
    {
        return method_exists($this, 'updateCard');
    }
}
