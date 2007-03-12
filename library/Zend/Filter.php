<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Filter.php 3278 2007-02-07 21:54:50Z darby $
 */


/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter implements Zend_Filter_Interface
{
    /**
     * Options for isHostname() that specify which types of hostnames
     * to allow.
     *
     * HOST_ALLOW_DNS:   Allows Internet domain names (e.g.,
     *                   example.com).
     * HOST_ALLOW_IP:    Allows IP addresses.
     * HOST_ALLOW_LOCAL: Allows local network names (e.g., localhost,
     *                   www.localdomain) and Internet domain names.
     * HOST_ALLOW_ALL:   Allows all of the above types of hostnames.
     *
     * @deprecated since 0.8.0
     */
    const HOST_ALLOW_DNS   = 1;
    const HOST_ALLOW_IP    = 2;
    const HOST_ALLOW_LOCAL = 4;
    const HOST_ALLOW_ALL   = 7;

    /**
     * Filter chain
     *
     * @var array
     */
    protected $_filters = array();

    /**
     * Adds a filter to the end of the chain
     *
     * @param  Zend_Filter_Interface $filter
     * @return Zend_Filter Provides a fluent interface
     */
    public function addFilter(Zend_Filter_Interface $filter)
    {
        $this->_filters[] = $filter;
        return $this;
    }

    /**
     * Returns $value filtered through each filter in the chain
     *
     * Filters are run in the order in which they were added to the chain (FIFO)
     *
     * @param  mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        $valueFiltered = $value;
        foreach ($this->_filters as $filter) {
            $valueFiltered = $filter->filter($valueFiltered);
        }
        return $valueFiltered;
    }

    /**
     * Returns only the alphabetic characters in value.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     string
     */
    public static function getAlpha($value)
    {
        require_once 'Zend/Filter/Alpha.php';
        $filter = new Zend_Filter_Alpha();
        return $filter->filter($value);
    }

    /**
     * Returns only the alphabetic characters and digits in value.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     string
     */
    public static function getAlnum($value)
    {
        require_once 'Zend/Filter/Alnum.php';
        $filter = new Zend_Filter_Alnum();
        return $filter->filter($value);
    }

    /**
     * Returns only the digits in value. This differs from getInt().
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     string
     */
    public static function getDigits($value)
    {
        require_once 'Zend/Filter/Digits.php';
        $filter = new Zend_Filter_Digits();
        return $filter->filter($value);
    }

    /**
     * Returns dirname(value).
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     string
     */
    public static function getDir($value)
    {
        require_once 'Zend/Filter/Dir.php';
        $filter = new Zend_Filter_Dir();
        return $filter->filter($value);
    }

    /**
     * Returns (int) value.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     integer
     */
    public static function getInt($value)
    {
        require_once 'Zend/Filter/Int.php';
        $filter = new Zend_Filter_Int();
        return $filter->filter($value);
    }

    /**
     * Returns realpath(value).
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     string
     */
    public static function getPath($value)
    {
        require_once 'Zend/Filter/RealPath.php';
        $filter = new Zend_Filter_RealPath();
        return $filter->filter($value);
    }

    /**
     * Returns TRUE if every character is alphabetic or a digit,
     * FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     boolean
     */
    public static function isAlnum($value)
    {
        require_once 'Zend/Validate/Alnum.php';
        $validator = new Zend_Validate_Alnum();
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if every character is alphabetic, FALSE
     * otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     boolean
     */
    public static function isAlpha($value)
    {
        require_once 'Zend/Validate/Alpha.php';
        $validator = new Zend_Validate_Alpha();
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value is greater than or equal to $min and less
     * than or equal to $max, FALSE otherwise. If $inc is set to
     * FALSE, then the value must be strictly greater than $min and
     * strictly less than $max.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @param      mixed $min
     * @param      mixed $max
     * @param      boolean $inc
     * @return     boolean
     */
    public static function isBetween($value, $min, $max, $inc = true)
    {
        require_once 'Zend/Validate/Between.php';
        $validator = new Zend_Validate_Between($min, $max, $inc);
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if it is a valid credit card number format.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     boolean
     */
    public static function isCcnum($value)
    {
        require_once 'Zend/Validate/Ccnum.php';
        $validator = new Zend_Validate_Ccnum();
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value is a valid date, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     boolean
     */
    public static function isDate($value)
    {
        require_once 'Zend/Validate/Date.php';
        $validator = new Zend_Validate_Date();
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if every character is a digit, FALSE otherwise.
     * This is just like isInt(), except there is no upper limit.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     boolean
     */
    public static function isDigits($value)
    {
        require_once 'Zend/Validate/Digits.php';
        $validator = new Zend_Validate_Digits();
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value is a valid email format, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     boolean
     */
    public static function isEmail($value)
    {
        require_once 'Zend/Validate/EmailAddress.php';
        $validator = new Zend_Validate_EmailAddress();
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value is a valid float value, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     boolean
     */
    public static function isFloat($value)
    {
        require_once 'Zend/Validate/Float.php';
        $validator = new Zend_Validate_Float();
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value is greater than $min, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @param      mixed $min
     * @return     boolean
     */
    public static function isGreaterThan($value, $min)
    {
        require_once 'Zend/Validate/GreaterThan.php';
        $validator = new Zend_Validate_GreaterThan($min);
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value is a valid hexadecimal format, FALSE
     * otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     boolean
     */
    public static function isHex($value)
    {
        require_once 'Zend/Validate/Hex.php';
        $validator = new Zend_Validate_Hex();
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value is a valid hostname, FALSE otherwise.
     * Depending upon the value of $allow, Internet domain names, IP
     * addresses, and/or local network names are considered valid.
     * The default is HOST_ALLOW_ALL, which considers all of the
     * above to be valid.
     *
     * @deprecated since 0.8.0
     * @param      mixed   $value
     * @param      integer $allow bitfield for HOST_ALLOW_DNS, HOST_ALLOW_IP, HOST_ALLOW_LOCAL
     * @return     boolean
     */
    public static function isHostname($value, $allow = self::HOST_ALLOW_ALL)
    {
        require_once 'Zend/Validate/Hostname.php';
        $validator = new Zend_Validate_Hostname($allow);
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value is a valid integer value, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     boolean
     */
    public static function isInt($value)
    {
        require_once 'Zend/Validate/Int.php';
        $validator = new Zend_Validate_Int();
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value is a valid IP format, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     boolean
     */
    public static function isIp($value)
    {
        require_once 'Zend/Validate/Ip.php';
        $validator = new Zend_Validate_Ip();
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value is less than $max, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @param      mixed $max
     * @return     boolean
     */
    public static function isLessThan($value, $max)
    {
        require_once 'Zend/Validate/LessThan.php';
        $validator = new Zend_Validate_LessThan($max);
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value is one of $allowed, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @param      mixed $allowed
     * @return     boolean
     */
    public static function isOneOf($value, array $allowed)
    {
        require_once 'Zend/Validate/InArray.php';
        $validator = new Zend_Validate_InArray($allowed);
        return $validator->isValid($value);
    }

    /**
     * Returns TRUE if value matches $pattern, FALSE otherwise. Uses
     * preg_match() for the matching.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @param      mixed $pattern
     * @return     boolean
     */
    public static function isRegex($value, $pattern)
    {
        require_once 'Zend/Validate/Regex.php';
        $validator = new Zend_Validate_Regex($pattern);
        return $validator->isValid($value);
    }

    /**
     * Returns value with all tags removed.
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     string
     */
    public static function noTags($value)
    {
        require_once 'Zend/Filter/StripTags.php';
        $filter = new Zend_Filter_StripTags();
        return $filter->filter($value);
    }

    /**
     * Returns basename(value).
     *
     * @deprecated since 0.8.0
     * @param      mixed $value
     * @return     string
     */
    public static function noPath($value)
    {
        require_once 'Zend/Filter/BaseName.php';
        $filter = new Zend_Filter_BaseName();
        return $filter->filter($value);
    }
}
