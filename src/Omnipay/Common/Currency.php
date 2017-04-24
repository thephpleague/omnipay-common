<?php
/**
 * Currency class
 */

namespace Omnipay\Common;

/**
 * Currency class
 *
 * This class abstracts certain functionality around currency objects,
 * currency codes and currency numbers relating to global currencies used
 * in the Omnipay system.
 */
final class Currency
{
    private $code;
    private $numeric;
    private $decimals;

    /**
     * Create a new Currency object
     */
    private function __construct($code, $numeric, $decimals)
    {
        $this->code = $code;
        $this->numeric = $numeric;
        $this->decimals = $decimals;
    }

    /**
     * Get the three letter code for the currency
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the numeric code for this currency
     *
     * @return string
     */
    public function getNumeric()
    {
        return $this->numeric;
    }

    /**
     * Get the number of decimal places for this currency
     *
     * @return int
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * Find a specific currency
     *
     * @param  string $code The three letter currency code
     * @return mixed  A Currency object, or null if no currency was found
     */
    public static function find($code)
    {
        $code = strtoupper($code);
        $currencies = static::all();

        if (isset($currencies[$code])) {
            return new static($code, $currencies[$code]['numeric'], $currencies[$code]['decimals']);
        }
    }


    /**
     * Get an array of all supported currencies
     *
     * @return array
     */
    public static function all()
    {
        return require __DIR__.'/../../../resources/currencies.php';
    }
}
