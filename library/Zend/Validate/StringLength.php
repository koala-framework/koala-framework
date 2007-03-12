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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: StringLength.php 3278 2007-02-07 21:54:50Z darby $
 */


/**
 * @see Zend_Validate_Interface
 */
require_once 'Zend/Validate/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_StringLength implements Zend_Validate_Interface
{
    /**
     * Minimum length
     *
     * @var integer
     */
    protected $_min;

    /**
     * Maximum length
     *
     * If null, there is no maximum length
     *
     * @var integer|null
     */
    protected $_max;

    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Sets validator options
     *
     * @param  mixed $min
     * @param  mixed $max
     * @return void
     */
    public function __construct($min = 0, $max = null)
    {
        $this->setMin($min);
        $this->setMax($max);
    }

    /**
     * Returns the min option
     *
     * @return integer
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Sets the min option
     *
     * @param  mixed $min
     * @return Zend_Validate_StringLength Provides a fluent interface
     */
    public function setMin($min)
    {
        $this->_min = max(0, (integer) $min);
        return $this;
    }

    /**
     * Returns the max option
     *
     * @return integer|null
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * Sets the max option
     *
     * @param  mixed $max
     * @return Zend_Validate_StringLength Provides a fluent interface
     */
    public function setMax($max)
    {
        if (null === $max) {
            $this->_max = null;
        } else {
            $this->_max = (integer) $max;
        }

        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if the string length of $value is at least the min option and
     * no greater than the max option (when the max option is not null).
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_messages = array();
        $length = strlen($value);
        if ($length < $this->_min) {
            $this->_messages[] = "'$value' is less than $this->_min characters long";
        }
        if (null !== $this->_max && $this->_max < $length) {
            $this->_messages[] = "'$value' is greater than $this->_max characters long";
        }
        if (count($this->_messages)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
