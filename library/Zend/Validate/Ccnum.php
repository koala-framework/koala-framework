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
 * @version    $Id: Ccnum.php 3278 2007-02-07 21:54:50Z darby $
 */


/**
 * @see Zend_Validate_Interface
 */
require_once 'Zend/Validate/Interface.php';


/**
 * @see Zend_Filter_Digits
 */
require_once 'Zend/Filter/Digits.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_Ccnum implements Zend_Validate_Interface
{
    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value follows the Luhn algorithm (mod-10 checksum)
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_messages = array();

        $filterDigits = new Zend_Filter_Digits();
        $valueFiltered = $filterDigits->filter($value);

        $length = strlen($valueFiltered);

        if ($length < 13 || $length > 19) {
            $this->_messages[] = "'$value' must contain between 13 and 19 digits";
            return false;
        }

        $sum    = 0;
        $weight = 2;

        for ($i = $length - 2; $i >= 0; $i--) {
            $digit = $weight * $valueFiltered[$i];
            $sum += floor($digit / 10) + $digit % 10;
            $weight = $weight % 2 + 1;
        }

        if ((10 - $sum % 10) % 10 != $valueFiltered[$length - 1]) {
            $this->_messages[] = "Luhn algorithm (mod-10 checksum) failed on '$valueFiltered'";
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
