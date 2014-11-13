<?php

namespace Omnipay\Common\Message;

/**
 * Barcode Payment Response interface
 * @package Omnipay\Common\Message
 */
interface BarcodePaymentResponseInterface extends AdvancedPaymentResponseInterface
{
    /**
     * Barcode absolute url or data URI
     * @return string
     */
    public function getBarcode();

    /**
     * Barcode content value
     * @return string
     */
    public function getBarcodeValue();
}
