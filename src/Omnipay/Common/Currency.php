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
class Currency
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
        return array(
            'AED' => array('numeric' => '784', 'decimals' => 2),
            'AFN' => array('numeric' => '971', 'decimals' => 2),
            'ALL' => array('numeric' => '008', 'decimals' => 2),
            'AMD' => array('numeric' => '051', 'decimals' => 2),
            'ANG' => array('numeric' => '532', 'decimals' => 2),
            'AOA' => array('numeric' => '973', 'decimals' => 2),
            'ARS' => array('numeric' => '032', 'decimals' => 2),
            'AUD' => array('numeric' => '036', 'decimals' => 2),
            'AWG' => array('numeric' => '533', 'decimals' => 2),
            'AZN' => array('numeric' => '944', 'decimals' => 2),
            'BAM' => array('numeric' => '977', 'decimals' => 2),
            'BBD' => array('numeric' => '052', 'decimals' => 2),
            'BDT' => array('numeric' => '050', 'decimals' => 2),
            'BGN' => array('numeric' => '975', 'decimals' => 2),
            'BHD' => array('numeric' => '048', 'decimals' => 3),
            'BIF' => array('numeric' => '108', 'decimals' => 0),
            'BMD' => array('numeric' => '060', 'decimals' => 2),
            'BND' => array('numeric' => '096', 'decimals' => 2),
            'BOB' => array('numeric' => '068', 'decimals' => 2),
            'BRL' => array('numeric' => '986', 'decimals' => 2),
            'BSD' => array('numeric' => '044', 'decimals' => 2),
            'BTC' => array('numeric' => null, 'decimals' => 8),
            'BTN' => array('numeric' => '064', 'decimals' => 2),
            'BWP' => array('numeric' => '072', 'decimals' => 2),
            'BYR' => array('numeric' => '974', 'decimals' => 0),
            'BZD' => array('numeric' => '084', 'decimals' => 2),
            'CAD' => array('numeric' => '124', 'decimals' => 2),
            'CDF' => array('numeric' => '976', 'decimals' => 2),
            'CHF' => array('numeric' => '756', 'decimals' => 2),
            'CLP' => array('numeric' => '152', 'decimals' => 0),
            'CNY' => array('numeric' => '156', 'decimals' => 2),
            'COP' => array('numeric' => '170', 'decimals' => 2),
            'CRC' => array('numeric' => '188', 'decimals' => 2),
            'CUC' => array('numeric' => '931', 'decimals' => 2),
            'CUP' => array('numeric' => '192', 'decimals' => 2),
            'CVE' => array('numeric' => '132', 'decimals' => 2),
            'CZK' => array('numeric' => '203', 'decimals' => 2),
            'DJF' => array('numeric' => '262', 'decimals' => 0),
            'DKK' => array('numeric' => '208', 'decimals' => 2),
            'DOP' => array('numeric' => '214', 'decimals' => 2),
            'DZD' => array('numeric' => '012', 'decimals' => 2),
            'EGP' => array('numeric' => '818', 'decimals' => 2),
            'ERN' => array('numeric' => '232', 'decimals' => 2),
            'ETB' => array('numeric' => '230', 'decimals' => 2),
            'EUR' => array('numeric' => '978', 'decimals' => 2),
            'FJD' => array('numeric' => '242', 'decimals' => 2),
            'FKP' => array('numeric' => '238', 'decimals' => 2),
            'GBP' => array('numeric' => '826', 'decimals' => 2),
            'GEL' => array('numeric' => '981', 'decimals' => 2),
            'GHS' => array('numeric' => '936', 'decimals' => 2),
            'GIP' => array('numeric' => '292', 'decimals' => 2),
            'GMD' => array('numeric' => '270', 'decimals' => 2),
            'GNF' => array('numeric' => '324', 'decimals' => 0),
            'GTQ' => array('numeric' => '320', 'decimals' => 2),
            'GYD' => array('numeric' => '328', 'decimals' => 2),
            'HKD' => array('numeric' => '344', 'decimals' => 2),
            'HNL' => array('numeric' => '340', 'decimals' => 2),
            'HRK' => array('numeric' => '191', 'decimals' => 2),
            'HTG' => array('numeric' => '332', 'decimals' => 2),
            'HUF' => array('numeric' => '348', 'decimals' => 2),
            'IDR' => array('numeric' => '360', 'decimals' => 2),
            'ILS' => array('numeric' => '376', 'decimals' => 2),
            'INR' => array('numeric' => '356', 'decimals' => 2),
            'IQD' => array('numeric' => '368', 'decimals' => 3),
            'IRR' => array('numeric' => '364', 'decimals' => 2),
            'ISK' => array('numeric' => '352', 'decimals' => 0),
            'JMD' => array('numeric' => '388', 'decimals' => 2),
            'JOD' => array('numeric' => '400', 'decimals' => 3),
            'JPY' => array('numeric' => '392', 'decimals' => 0),
            'KES' => array('numeric' => '404', 'decimals' => 2),
            'KGS' => array('numeric' => '417', 'decimals' => 2),
            'KHR' => array('numeric' => '116', 'decimals' => 2),
            'KMF' => array('numeric' => '174', 'decimals' => 0),
            'KPW' => array('numeric' => '408', 'decimals' => 2),
            'KRW' => array('numeric' => '410', 'decimals' => 0),
            'KWD' => array('numeric' => '414', 'decimals' => 3),
            'KYD' => array('numeric' => '136', 'decimals' => 2),
            'KZT' => array('numeric' => '398', 'decimals' => 2),
            'LAK' => array('numeric' => '418', 'decimals' => 0),
            'LBP' => array('numeric' => '422', 'decimals' => 2),
            'LKR' => array('numeric' => '144', 'decimals' => 2),
            'LRD' => array('numeric' => '430', 'decimals' => 2),
            'LSL' => array('numeric' => '426', 'decimals' => 2),
            'LYD' => array('numeric' => '434', 'decimals' => 3),
            'MAD' => array('numeric' => '504', 'decimals' => 2),
            'MDL' => array('numeric' => '498', 'decimals' => 2),
            'MGA' => array('numeric' => '969', 'decimals' => 0),
            'MKD' => array('numeric' => '807', 'decimals' => 2),
            'MMK' => array('numeric' => '104', 'decimals' => 2),
            'MNT' => array('numeric' => '496', 'decimals' => 2),
            'MOP' => array('numeric' => '446', 'decimals' => 2),
            'MRO' => array('numeric' => '478', 'decimals' => 0),
            'MUR' => array('numeric' => '480', 'decimals' => 2),
            'MVR' => array('numeric' => '462', 'decimals' => 2),
            'MWK' => array('numeric' => '454', 'decimals' => 2),
            'MXN' => array('numeric' => '484', 'decimals' => 2),
            'MYR' => array('numeric' => '458', 'decimals' => 2),
            'MZN' => array('numeric' => '943', 'decimals' => 2),
            'NAD' => array('numeric' => '516', 'decimals' => 2),
            'NGN' => array('numeric' => '566', 'decimals' => 2),
            'NIO' => array('numeric' => '558', 'decimals' => 2),
            'NOK' => array('numeric' => '578', 'decimals' => 2),
            'NPR' => array('numeric' => '524', 'decimals' => 2),
            'NZD' => array('numeric' => '554', 'decimals' => 2),
            'OMR' => array('numeric' => '512', 'decimals' => 3),
            'PAB' => array('numeric' => '590', 'decimals' => 2),
            'PEN' => array('numeric' => '604', 'decimals' => 2),
            'PGK' => array('numeric' => '598', 'decimals' => 2),
            'PHP' => array('numeric' => '608', 'decimals' => 2),
            'PKR' => array('numeric' => '586', 'decimals' => 2),
            'PLN' => array('numeric' => '985', 'decimals' => 2),
            'PYG' => array('numeric' => '600', 'decimals' => 0),
            'QAR' => array('numeric' => '634', 'decimals' => 2),
            'RON' => array('numeric' => '946', 'decimals' => 2),
            'RSD' => array('numeric' => '941', 'decimals' => 0),
            'RUB' => array('numeric' => '643', 'decimals' => 2),
            'RWF' => array('numeric' => '646', 'decimals' => 0),
            'SAR' => array('numeric' => '682', 'decimals' => 2),
            'SBD' => array('numeric' => '090', 'decimals' => 2),
            'SCR' => array('numeric' => '690', 'decimals' => 2),
            'SDG' => array('numeric' => '938', 'decimals' => 2),
            'SEK' => array('numeric' => '752', 'decimals' => 2),
            'SGD' => array('numeric' => '702', 'decimals' => 2),
            'SHP' => array('numeric' => '654', 'decimals' => 2),
            'SLL' => array('numeric' => '694', 'decimals' => 2),
            'SOS' => array('numeric' => '706', 'decimals' => 2),
            'SRD' => array('numeric' => '968', 'decimals' => 2),
            'SSP' => array('numeric' => '728', 'decimals' => 2),
            'STD' => array('numeric' => '678', 'decimals' => 2),
            'SYP' => array('numeric' => '760', 'decimals' => 2),
            'SZL' => array('numeric' => '748', 'decimals' => 2),
            'THB' => array('numeric' => '764', 'decimals' => 2),
            'TJS' => array('numeric' => '972', 'decimals' => 2),
            'TMT' => array('numeric' => '934', 'decimals' => 2),
            'TND' => array('numeric' => '788', 'decimals' => 3),
            'TOP' => array('numeric' => '776', 'decimals' => 2),
            'TRY' => array('numeric' => '949', 'decimals' => 2),
            'TTD' => array('numeric' => '780', 'decimals' => 2),
            'TWD' => array('numeric' => '901', 'decimals' => 2),
            'TZS' => array('numeric' => '834', 'decimals' => 2),
            'UAH' => array('numeric' => '980', 'decimals' => 2),
            'UGX' => array('numeric' => '800', 'decimals' => 0),
            'USD' => array('numeric' => '840', 'decimals' => 2),
            'UYU' => array('numeric' => '858', 'decimals' => 2),
            'UZS' => array('numeric' => '860', 'decimals' => 2),
            'VEF' => array('numeric' => '937', 'decimals' => 2),
            'VND' => array('numeric' => '704', 'decimals' => 0),
            'VUV' => array('numeric' => '548', 'decimals' => 0),
            'WST' => array('numeric' => '882', 'decimals' => 2),
            'XAF' => array('numeric' => '950', 'decimals' => 0),
            'XCD' => array('numeric' => '951', 'decimals' => 2),
            'XOF' => array('numeric' => '952', 'decimals' => 0),
            'XPF' => array('numeric' => '953', 'decimals' => 0),
            'YER' => array('numeric' => '886', 'decimals' => 2),
            'ZAR' => array('numeric' => '710', 'decimals' => 2),
            'ZMW' => array('numeric' => '967', 'decimals' => 2),
        );
    }
}
