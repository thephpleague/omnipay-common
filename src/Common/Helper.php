<?php
/**
 * Helper class
 */

namespace Omnipay\Common;

use InvalidArgumentException;

/**
 * Helper class
 *
 * This class defines various static utility functions that are in use
 * throughout the Omnipay system.
 */
class Helper
{
    /**
     * Sets the account gateway suffix so that a namespace might
     * look like '\Omnipay\Test\Account\Gateway'
     * @var string
     */
    protected static $accountNamespaceSuffix = 'Account';

    /**
     * Sets the user gateway suffix so that a namespace might
     * look like '\Omnipay\Test\User\Gateway'
     * @var string
     */
    protected static $userNamespaceSuffix = 'User';

    /**
     * Convert a string to camelCase. Strings already in camelCase will not be harmed.
     *
     * @param  string  $str The input string
     * @return string camelCased output string
     */
    public static function camelCase($str)
    {
        $str = self::convertToLowercase($str);
        return preg_replace_callback(
            '/_([a-z])/',
            function ($match) {
                return strtoupper($match[1]);
            },
            $str
        );
    }

    /**
     * Convert strings with underscores to be all lowercase before camelCase is preformed.
     *
     * @param  string $str The input string
     * @return string The output string
     */
    protected static function convertToLowercase($str)
    {
        $explodedStr = explode('_', $str);
        $lowercasedStr = [];

        if (count($explodedStr) > 1) {
            foreach ($explodedStr as $value) {
                $lowercasedStr[] = strtolower($value);
            }
            $str = implode('_', $lowercasedStr);
        }

        return $str;
    }

    /**
     * Validate a card number according to the Luhn algorithm.
     *
     * @param  string  $number The card number to validate
     * @return boolean True if the supplied card number is valid
     */
    public static function validateLuhn($number)
    {
        $str = '';
        foreach (array_reverse(str_split($number)) as $i => $c) {
            $str .= $i % 2 ? $c * 2 : $c;
        }

        return array_sum(str_split($str)) % 10 === 0;
    }

    /**
     * Initialize an object with a given array of parameters
     *
     * Parameters are automatically converted to camelCase. Any parameters which do
     * not match a setter on the target object are ignored.
     *
     * @param mixed $target     The object to set parameters on
     * @param array $parameters An array of parameters to set
     */
    public static function initialize($target, array $parameters = null)
    {
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $method = 'set'.ucfirst(static::camelCase($key));
                if (method_exists($target, $method)) {
                    $target->$method($value);
                }
            }
        }
    }

    /**
     * Resolve a gateway class to a short name.
     *
     * The short name can be used with GatewayFactory as an alias of the gateway class,
     * to create new instances of a gateway.
     */
    public static function getGatewayShortName($className)
    {
        if (0 === strpos($className, '\\')) {
            $className = substr($className, 1);
        }

        if (0 === strpos($className, 'Omnipay\\')) {
            return trim(str_replace('\\', '_', substr($className, 8, -7)), '_');
        }

        return '\\'.$className;
    }

    /**
     * Resolve a short gateway name to a full namespaced gateway class.
     *
     * Class names beginning with a namespace marker (\) are left intact.
     * Non-namespaced classes are expected to be in the \Omnipay namespace, e.g.:
     *
     *      \Custom\Gateway     => \Custom\Gateway
     *      \Custom_Gateway     => \Custom_Gateway
     *      Stripe              => \Omnipay\Stripe\Gateway
     *      PayPal\Express      => \Omnipay\PayPal\ExpressGateway
     *      PayPal_Express      => \Omnipay\PayPal\ExpressGateway
     *
     * @param  string  $shortName The short gateway name
     * @return string  The fully namespaced gateway class name
     */
    public static function getGatewayClassName($shortName)
    {
        if (0 === strpos($shortName, '\\')) {
            return $shortName;
        }

        // replace underscores with namespace marker, PSR-0 style
        $shortName = str_replace('_', '\\', $shortName);
        if (false === strpos($shortName, '\\')) {
            $shortName .= '\\';
        }

        return '\\Omnipay\\'.$shortName.'Gateway';
    }

    /**
     * Gets the gateway name for account processes
     *
     * @param  string $shortName the short gateway name
     * @return string  the full namespaced gateway class name
     */
    public static function getAccountGatewayClassName($shortName)
    {
        return self::gatewayClassNameModify(
            self::getGatewayClassName($shortName),
            self::$accountNamespaceSuffix
        );
    }

    /**
     * Gets the gateway name for user processes
     *
     * @param  string $shortName the short gateway name
     * @return string  the full namespaced gateway class name
     */
    public static function getUserGatewayClassName($shortName)
    {
        return self::gatewayClassNameModify(
            self::getGatewayClassName($shortName),
            self::$userNamespaceSuffix
        );
    }

    /**
     * Appends to the namespace of the gateway class name
     *
     * @param  string $classname  the full classname
     * @param  string $appendWith what to add to the classname just before \\Gateway
     * @return string
     */
    public static function gatewayClassNameModify($classname, $appendWith = '')
    {
        $replaceWith = '\\Gateway';
        if (!empty($appendWith)) {
            $replaceWith = '\\' . $appendWith . $replaceWith;
        }

        return preg_replace('/\\\Gateway/', $replaceWith, $classname);
    }

    /**
     * gets the shortname for an account gateway
     *
     * @param  string $className the full classname
     * @return string
     */
    public static function getAccountGatewayShortName($className)
    {
        return self::replaceGatewaySuffix(
            $className,
            self::$accountNamespaceSuffix
        );
    }

    /**
     * Get's the shortname for a user gateway
     *
     * @param  string $className the full classname
     * @return string
     */
    public static function getUserGatewayShortName($className)
    {
        return self::replaceGatewaySuffix(
            $className,
            self::$userNamespaceSuffix
        );
    }

    /**
     * Replaces the gateway suffix if one had been appended with gatewayClassNameModify
     *
     * @param  string $classname  the full classname
     * @param  string $appended   the string that had been added to the classname just before \\Gateway
     * @return string
     */
    public static function replaceGatewaySuffix($className, $appended = '')
    {
        // trim any leading backslashes
        $testClassName = ltrim($className, '\\');

        $suffix = '\\Gateway';

        if (!empty($appended)) {
            // first add our
            if (0 !== strpos($appended, '\\')) {
                $appended = '\\' . $appended;
            }

            $suffix = $appended . $suffix;
        }

        if (0 === strpos($testClassName, 'Omnipay\\')) {
            return trim(
                str_replace(
                    '\\',
                    '_',
                    substr(
                        $testClassName,
                        8,
                        -(strlen($suffix))
                    )
                ),
                '_'
            );
        }

        return $className;
    }
}
