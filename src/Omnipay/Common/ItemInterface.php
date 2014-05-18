<?php

namespace Omnipay\Common;

/**
 * Cart Item interface
 */
interface ItemInterface
{
    /**
     * Name of the item
     */
    public function getName();

    /**
     * Description of the item
     */
    public function getDescription();

    /**
     * Quantity of the item
     */
    public function getQuantity();

    /**
     * Tax amount of the item
     */
    public function getTax();

    /**
     * Price of the item
     */
    public function getPrice();

    /**
     * Full price of the item
     */
    public function getFullPrice();
}
