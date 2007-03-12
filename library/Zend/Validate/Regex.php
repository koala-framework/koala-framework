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
 * @version    $Id: Regex.php 3278 2007-02-07 21:54:50Z darby $
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
class Zend_Validate_Regex implements Zend_Validate_Interface
{
    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected $_pattern;

    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Sets validator options
     *
     * @param  mixed $pattern
     * @return void
     */
    public function __construct($pattern)
    {
        $this->setPattern($pattern);
    }

    /**
     * Returns the pattern option
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     * Sets the pattern option
     *
     * @param  mixed $pattern
     * @return Zend_Validate_Regex Provides a fluent interface
     */
    public function setPattern($pattern)
    {
        $this->_pattern = (string) $pattern;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value matches against the pattern option
     *
     * @param  mixed $value
     * @throws Zend_Validate_Exception if there is a fatal error in pattern matching
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_messages = array();
        $status = @preg_match($this->_pattern, $value);
        if (false === $status) {
            /**
             * @see Zend_Validate_Exception
             */
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("Internal error matching pattern '$this->_pattern' against value '$value'");
        }
        if (!$status) {
            $this->_messages[] = "'$value' does not match against pattern '$this->_pattern'";
            return false;
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
