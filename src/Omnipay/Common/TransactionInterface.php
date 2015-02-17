<?php

namespace Omnipay\Common;

/**
 * Transaction Interface
 */
interface TransactionInterface
{
    /**
     * Id of the transaction
     */
    public function getId();

    /**
     * Reference of the transaction
     */
    public function getReference();

    /**
     * Amount of the transaction
     */
    public function getAmount();

    /**
     * Currency of the transaction
     * @return Currency
     */
    public function getCurrency();

    /**
     * Payment method of the transaction
     * @return PaymentMethod
     */
    public function getPaymentMethod();

    /**
     * Issuer of the transaction
     * @return Issuer
     */
    public function getIssuer();

    /**
     * Credit card and payer details of the transaction
     * @return CreditCard
     */
    public function getCreditCard();

    /**
     * Return true if transaction is pending
     * @return bool
     */
    public function isPending();

    /**
     * Return true if transaction is processing
     * @return bool
     */
    public function isProcessing();

    /**
     * Return true if transaction is success
     * @return bool
     */
    public function isSuccess();

    /**
     * Return true if transaction is denied
     * @return bool
     */
    public function isDenied();

    /**
     * Return true if transaction is reversed
     * @return bool
     */
    public function isReversed();

    /**
     * Return true if transaction has failed
     * @return bool
     */
    public function isError();

    /**
     * Return true if is a testing transaction
     */
    public function isTest();
}
