<?php
/**
 * Abstract Payment Request
 */

namespace Omnipay\Common\Message;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Number;
use Money\Parser\DecimalMoneyParser;
use Omnipay\Common\CreditCard;
use Omnipay\Common\ItemBag;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Abstract Payment Request
 *
 * This abstract class implements RequestInterface and defines a basic
 * set of functions that all Omnipay Requests are intended to include.
 *
 * Requests of this class are usually created using the createRequest
 * function of the gateway and then actioned using methods within this
 * class or a class that extends this class.
 *
 * Example -- creating a request:
 *
 * <code>
 *   class MyRequest extends \Omnipay\Common\Message\AbstractRequest {};
 *
 *   class MyGateway extends \Omnipay\Common\AbstractGateway {
 *     function myRequest($parameters) {
 *       $this->createRequest('MyRequest', $parameters);
 *     }
 *   }
 *
 *   // Create the gateway object
 *   $gw = Omnipay::create('MyGateway');
 *
 *   // Create the request object
 *   $myRequest = $gw->myRequest($someParameters);
 * </code>
 *
 * Example -- validating and sending a request:
 *
 * <code>
 *   try {
 *     $myRequest->validate();
 *     $myResponse = $myRequest->send();
 *   } catch (InvalidRequestException $e) {
 *     print "Something went wrong: " . $e->getMessage() . "\n";
 *   }
 *   // now do something with the $myResponse object, test for success, etc.
 * </code>
 *
 */
abstract class AbstractRequest extends AbstractGenericRequest
{

    /**
     * @var ISOCurrencies
     */
    protected $currencies;

    /**
     * @var bool
     */
    protected $zeroAmountAllowed = true;

    /**
     * @var bool
     */
    protected $negativeAmountAllowed = false;

    /**
     * Get the card.
     *
     * @return CreditCard
     */
    public function getCard()
    {
        return $this->getParameter('card');
    }

    /**
     * Sets the card.
     *
     * @param CreditCard $value
     * @return $this
     */
    public function setCard($value)
    {
        if ($value && !$value instanceof CreditCard) {
            $value = new CreditCard($value);
        }

        return $this->setParameter('card', $value);
    }

    /**
     * Get the card token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->getParameter('token');
    }

    /**
     * Sets the card token.
     *
     * @param string $value
     * @return $this
     */
    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    /**
     * Get the card reference.
     *
     * @return string
     */
    public function getCardReference()
    {
        return $this->getParameter('cardReference');
    }

    /**
     * Sets the card reference.
     *
     * @param string $value
     * @return $this
     */
    public function setCardReference($value)
    {
        return $this->setParameter('cardReference', $value);
    }

    /**
     * @return ISOCurrencies
     */
    protected function getCurrencies()
    {
        if ($this->currencies === null) {
            $this->currencies = new ISOCurrencies();
        }

        return $this->currencies;
    }

    /**
     * @param  string|int|null $amount
     * @return null|Money
     * @throws InvalidRequestException
     */
    private function getMoney($amount = null)
    {
        $currencyCode = $this->getCurrency() ?: 'USD';
        $currency = new Currency($currencyCode);

        $amount = $amount !== null ? $amount : $this->getParameter('amount');

        if ($amount === null) {
            return null;
        } elseif ($amount instanceof Money) {
            $money = $amount;
        } elseif (is_integer($amount)) {
            $money = new Money($amount, $currency);
        } else {
            $moneyParser = new DecimalMoneyParser($this->getCurrencies());

            $number = Number::fromString($amount);

            // Check for rounding that may occur if too many significant decimal digits are supplied.
            $decimal_count = strlen($number->getFractionalPart());
            $subunit = $this->getCurrencies()->subunitFor($currency);
            if ($decimal_count > $subunit) {
                throw new InvalidRequestException('Amount precision is too high for currency.');
            }

            $money = $moneyParser->parse((string) $number, $currency);
        }

        // Check for a negative amount.
        if (!$this->negativeAmountAllowed && $money->isNegative()) {
            throw new InvalidRequestException('A negative amount is not allowed.');
        }

        // Check for a zero amount.
        if (!$this->zeroAmountAllowed && $money->isZero()) {
            throw new InvalidRequestException('A zero amount is not allowed.');
        }

        return $money;
    }

    /**
     * Validates and returns the formatted amount.
     *
     * @throws InvalidRequestException on any validation failure.
     * @return string The amount formatted to the correct number of decimal places for the selected currency.
     */
    public function getAmount()
    {
        $money = $this->getMoney();

        if ($money !== null) {
            $moneyFormatter = new DecimalMoneyFormatter($this->getCurrencies());

            return $moneyFormatter->format($money);
        }
    }

    /**
     * Sets the payment amount.
     *
     * @param string|null $value
     * @return $this
     */
    public function setAmount($value)
    {
        return $this->setParameter('amount', $value !== null ? (string) $value : null);
    }

    /**
     * Get the payment amount as an integer.
     *
     * @return integer
     */
    public function getAmountInteger()
    {
        $money = $this->getMoney();

        if ($money !== null) {
            return (int) $money->getAmount();
        }
    }

    /**
     * Sets the payment amount as integer.
     *
     * @param int $value
     * @return $this
     */
    public function setAmountInteger($value)
    {
        return $this->setParameter('amount', (int) $value);
    }

    /**
     * Sets the payment amount as integer.
     *
     * @param Money $value
     * @return $this
     */
    public function setMoney(Money $value)
    {
        $currency = $value->getCurrency()->getCode();

        $this->setCurrency($currency);

        return $this->setParameter('amount', $value);
    }

    /**
     * Get the payment currency code.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->getParameter('currency');
    }

    /**
     * Sets the payment currency code.
     *
     * @param string $value
     * @return $this
     */
    public function setCurrency($value)
    {
        if ($value !== null) {
            $value = strtoupper($value);
        }
        return $this->setParameter('currency', $value);
    }

    /**
     * Get the payment currency number.
     *
     * @return string|null
     */
    public function getCurrencyNumeric()
    {
        if (! $this->getCurrency()) {
            return null;
        }

        $currency = new Currency($this->getCurrency());

        if ($this->getCurrencies()->contains($currency)) {
            return (string) $this->getCurrencies()->numericCodeFor($currency);
        }
    }

    /**
     * Get the number of decimal places in the payment currency.
     *
     * @return integer
     */
    public function getCurrencyDecimalPlaces()
    {
        if ($this->getCurrency()) {
            $currency = new Currency($this->getCurrency());
            if ($this->getCurrencies()->contains($currency)) {
                return $this->getCurrencies()->subunitFor($currency);
            }
        }

        return 2;
    }

    /**
     * Format an amount for the payment currency.
     *
     * @param string $amount
     * @return string
     */
    public function formatCurrency($amount)
    {
        $money = $this->getMoney((string) $amount);
        $formatter = new DecimalMoneyFormatter($this->getCurrencies());

        return $formatter->format($money);
    }

    /**
     * Get the transaction ID.
     *
     * The transaction ID is the identifier generated by the merchant website.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getParameter('transactionId');
    }

    /**
     * Sets the transaction ID.
     *
     * @param string $value
     * @return $this
     */
    public function setTransactionId($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    /**
     * Get the transaction reference.
     *
     * The transaction reference is the identifier generated by the remote
     * payment gateway.
     *
     * @return string
     */
    public function getTransactionReference()
    {
        return $this->getParameter('transactionReference');
    }

    /**
     * Sets the transaction reference.
     *
     * @param string $value
     * @return $this
     */
    public function setTransactionReference($value)
    {
        return $this->setParameter('transactionReference', $value);
    }

    /**
     * A list of items in this order
     *
     * @return ItemBag|null A bag containing items in this order
     */
    public function getItems()
    {
        return $this->getParameter('items');
    }

    /**
     * Set the items in this order
     *
     * @param ItemBag|array $items An array of items in this order
     * @return $this
     */
    public function setItems($items)
    {
        if ($items && !$items instanceof ItemBag) {
            $items = new ItemBag($items);
        }

        return $this->setParameter('items', $items);
    }

    /**
     * Get the client IP address.
     *
     * @return string
     */
    public function getClientIp()
    {
        return $this->getParameter('clientIp');
    }

    /**
     * Sets the client IP address.
     *
     * @param string $value
     * @return $this
     */
    public function setClientIp($value)
    {
        return $this->setParameter('clientIp', $value);
    }

    /**
     * Get the request return URL.
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->getParameter('returnUrl');
    }

    /**
     * Sets the request return URL.
     *
     * @param string $value
     * @return $this
     */
    public function setReturnUrl($value)
    {
        return $this->setParameter('returnUrl', $value);
    }

    /**
     * Get the request cancel URL.
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getParameter('cancelUrl');
    }

    /**
     * Sets the request cancel URL.
     *
     * @param string $value
     * @return $this
     */
    public function setCancelUrl($value)
    {
        return $this->setParameter('cancelUrl', $value);
    }

    /**
     * Get the request notify URL.
     *
     * @return string
     */
    public function getNotifyUrl()
    {
        return $this->getParameter('notifyUrl');
    }

    /**
     * Sets the request notify URL.
     *
     * @param string $value
     * @return $this
     */
    public function setNotifyUrl($value)
    {
        return $this->setParameter('notifyUrl', $value);
    }

    /**
     * Get the payment issuer.
     *
     * This field is used by some European gateways, and normally represents
     * the bank where an account is held (separate from the card brand).
     *
     * @return string
     */
    public function getIssuer()
    {
        return $this->getParameter('issuer');
    }

    /**
     * Set the payment issuer.
     *
     * This field is used by some European gateways, and normally represents
     * the bank where an account is held (separate from the card brand).
     *
     * @param string $value
     * @return $this
     */
    public function setIssuer($value)
    {
        return $this->setParameter('issuer', $value);
    }

    /**
     * Get the payment issuer.
     *
     * This field is used by some European gateways, which support
     * multiple payment providers with a single API.
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->getParameter('paymentMethod');
    }

    /**
     * Set the payment method.
     *
     * This field is used by some European gateways, which support
     * multiple payment providers with a single API.
     *
     * @param string $value
     * @return $this
     */
    public function setPaymentMethod($value)
    {
        return $this->setParameter('paymentMethod', $value);
    }
}
