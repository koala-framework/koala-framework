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
 * @version    $Id: Between.php 3278 2007-02-07 21:54:50Z darby $
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
class Zend_Validate_Between implements Zend_Validate_Interface
{
    /**
     * Minimum value
     *
     * @var mixed
     */
    protected $_min;

    /**
     * Maximum value
     *
     * @var mixed
     */
    protected $_max;

    /**
     * Whether to do inclusive comparisons, allowing equivalence to min and/or max
     *
     * If false, then strict comparisons are done, and the value may equal neither
     * the min or max options
     *
     * @var boolean
     */
    protected $_inclusive;

    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Sets validator options
     *
     * @param  mixed   $min
     * @param  mixed   $max
     * @param  boolean $inclusive
     * @return void
     */
    public function __construct($min, $max, $inclusive = true)
    {
        $this->setMin($min)
             ->setMax($max)
             ->setInclusive($inclusive);
    }

    /**
     * Returns the min option
     *
     * @return mixed
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Sets the min option
     *
     * @param  mixed $min
     * @return Zend_Validate_Between Provides a fluent interface
     */
    public function setMin($min)
    {
        $this->_min = $min;
        return $this;
    }

    /**
     * Returns the max option
     *
     * @return mixed
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * Sets the max option
     *
     * @param  mixed $max
     * @return Zend_Validate_Between Provides a fluent interface
     */
    public function setMax($max)
    {
        $this->_max = $max;
        return $this;
    }

    /**
     * Returns the inclusive option
     *
     * @return boolean
     */
    public function getInclusive()
    {
        return $this->_inclusive;
    }

    /**
     * Sets the inclusive option
     *
     * @param  boolean $inclusive
     * @return Zend_Validate_Between Provides a fluent interface
     */
    public function setInclusive($inclusive)
    {
        $this->_inclusive = $inclusive;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is between min and max options, inclusively
     * if inclusive option is true.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_messages = array();
        if ($this->_inclusive) {
            if ($this->_min > $value || $value > $this->_max) {
                $this->_messages[] = "'$value' is not between '$this->_min' and '$this->_max', inclusively";
                return false;
            }
        } else {
            if ($this->_min >= $value || $value >= $this->_max) {
                $this->_messages[] = "'$value' is not strictly between '$this->_min' and '$this->_max'";
                return false;
            }
        }
        return true;
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
