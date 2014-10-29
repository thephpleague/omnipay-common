<?php

namespace Omnipay\Common\Message;

/**
 * Interface Fetch Transaction Response interface
 * @package Omnipay\Common\Message
 */
interface FetchTransactionResponseInterface extends ResponseInterface
{
    /**
     * @return \Omnipay\Common\TransactionInterface
     */
    public function getTransaction();
}
