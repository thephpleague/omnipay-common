<?php


namespace Omnipay\Common\Message;

/**
 * Advanced Payment Response interface
 * @package Omnipay\Common\Message
 */
interface AdvancedPaymentResponseInterface extends ResponseInterface
{
    /**
     * Is the response declined?
     *
     * @return boolean
     */
    public function isDeclined();

    /**
     * Is the response pending?
     *
     * @return boolean
     */
    public function isPending();

    /**
     * Does the response require a redirect?
     *
     * @return boolean
     */
    public function isError();

    /**
     * Return payment method
     *
     * @return \Omnipay\Common\PaymentMethod|null
     */
    public function getPaymentMethod();

    /**
     * Return payment issuer
     *
     * @return \Omnipay\Common\Issuer|null
     */
    public function getIssuer();

    /**
     * Authorization code returned from gateway
     *
     * @return string|null
     */
    public function getAuthorizationCode();

    /**
     * Internal reference. Used together the transactionReference by somes gateways
     *
     * @return string|null
     */
    public function getInternalReference();

    /**
     * Return expiration date for this operation
     *
     * @return \DateTime|null
     */
    public function getExpirationDate();
}
