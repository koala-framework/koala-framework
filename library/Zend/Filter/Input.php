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
 * @subpackage Input
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Input.php 3278 2007-02-07 21:54:50Z darby $
 */


/**
 * @see Zend_Filter
 */
require_once 'Zend/Filter.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage Input
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Input
{
    /**
     * Input data source
     *
     * @var array
     */
    protected $_source = null;

    /**
     * Stores a reference to the data source array and removes the reference
     * $source when $strict is true
     *
     * @param  array   $source
     * @param  boolean $strict
     * @return void
     */
    public function __construct(array &$source, $strict = true)
    {
        $this->_source = $source;

        if ($strict) {
            $source = null;
        }
    }

    /**
     * Returns only the alphabetic characters in value.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function getAlpha($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        return Zend_Filter::getAlpha($this->_source[$key]);
    }

    /**
     * Returns only the alphabetic characters and digits in value.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function getAlnum($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        return Zend_Filter::getAlnum($this->_source[$key]);
    }

    /**
     * Returns only the digits in value. This differs from getInt().
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function getDigits($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        return Zend_Filter::getDigits($this->_source[$key]);
    }

    /**
     * Returns dirname(value).
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function getDir($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        return Zend_Filter::getDir($this->_source[$key]);
    }

    /**
     * Returns (int) value.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function getInt($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        return Zend_Filter::getInt($this->_source[$key]);
    }

    /**
     * Returns realpath(value).
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function getPath($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        return Zend_Filter::getPath($this->_source[$key]);
    }

    /**
     * Returns value.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function getRaw($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        return $this->_source[$key];
    }

    /**
     * Returns value if every character is alphabetic or a digit,
     * FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testAlnum($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isAlnum($this->_source[$key])) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if every character is alphabetic, FALSE
     * otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testAlpha($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isAlpha($this->_source[$key])) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it is greater than or equal to $min and less
     * than or equal to $max, FALSE otherwise. If $inc is set to
     * FALSE, then the value must be strictly greater than $min and
     * strictly less than $max.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @param      mixed $min
     * @param      mixed $max
     * @param      boolean $inc
     * @return     mixed
     */
    public function testBetween($key, $min, $max, $inc = true)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isBetween($this->_source[$key], $min, $max, $inc)) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it is a valid credit card number format. The
     * optional second argument allows developers to indicate the
     * type.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testCcnum($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isCcnum($this->_source[$key])) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns $value if it is a valid date, FALSE otherwise. The
     * date is required to be in ISO 8601 format.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testDate($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isDate($this->_source[$key])) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if every character is a digit, FALSE otherwise.
     * This is just like isInt(), except there is no upper limit.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testDigits($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isDigits($this->_source[$key])) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it is a valid email format, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testEmail($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isEmail($this->_source[$key])) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it is a valid float value, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testFloat($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isFloat($this->_source[$key])) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it is greater than $min, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testGreaterThan($key, $min)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isGreaterThan($this->_source[$key], $min)) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it is a valid hexadecimal format, FALSE
     * otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testHex($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isHex($this->_source[$key])) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it is a valid hostname, FALSE otherwise.
     * Depending upon the value of $allow, Internet domain names, IP
     * addresses, and/or local network names are considered valid.
     * The default is HOST_ALLOW_ALL, which considers all of the
     * above to be valid.
     *
     * @deprecated since 0.8.0
     * @param      mixed   $key
     * @param      integer $allow bitfield for HOST_ALLOW_DNS, HOST_ALLOW_IP, HOST_ALLOW_LOCAL
     * @return     mixed
     */
    public function testHostname($key, $allow = Zend_Filter::HOST_ALLOW_ALL)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isHostname($this->_source[$key], $allow)) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it is a valid integer value, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testInt($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isInt($this->_source[$key])) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it is a valid IP format, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testIp($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isIp($this->_source[$key])) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it is less than $max, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @param      mixed $max
     * @return     mixed
     */
    public function testLessThan($key, $max)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isLessThan($this->_source[$key], $max)) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it is one of $allowed, FALSE otherwise.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function testOneOf($key, array $allowed)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isOneOf($this->_source[$key], $allowed)) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value if it matches $pattern, FALSE otherwise. Uses
     * preg_match() for the matching.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @param      mixed $pattern
     * @return     mixed
     */
    public function testRegex($key, $pattern)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        if (Zend_Filter::isRegex($this->_source[$key], $pattern)) {
            return $this->_source[$key];
        }

        return false;
    }

    /**
     * Returns value with all tags removed.
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function noTags($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        return Zend_Filter::noTags($this->_source[$key]);
    }

    /**
     * Returns basename(value).
     *
     * @deprecated since 0.8.0
     * @param      mixed $key
     * @return     mixed
     */
    public function noPath($key)
    {
        if (!$this->keyExists($key)) {
            return false;
        }
        return Zend_Filter::noPath($this->_source[$key]);
    }

    /**
     * Checks if a key exists
     *
     * @param  mixed $key
     * @return boolean
     */
    public function keyExists($key)
    {
       return array_key_exists($key, $this->_source);
    }
}
