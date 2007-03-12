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
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Abstract.php 3272 2007-02-07 20:17:03Z gavin $
 * @since      Preview Release 0.2
 */

/**
 * Zend_Session_Abstract
 *
 * @category Zend
 * @package Zend_Session
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Session_Abstract
{
    /**
     * Whether or not session permits writing (modification of $_SESSION[])
     *
     * @var bool
     */
    static protected $_writable = false;

    /**
     * Whether or not session permits reading (reading data in $_SESSION[])
     *
     * @var bool
     */
    static protected $_readable = false;

    /**
     * Since expiring data is handled at startup to avoid __destruct difficulties,
     * the data that will be expiring at end of this request is held here
     *
     * @var array
     */
    static protected $_expiringData = array();


    /**
     * Error message thrown when an action requires modification,
     * but current Zend_Session has been marked as read-only.
     */
     const _THROW_NOT_WRITABLE_MSG = 'Zend_Session is currently marked as read-only.';


    /**
     * Error message thrown when an action requires reading session data,
     * but current Zend_Session is not marked as readable.
     */
     const _THROW_NOT_READABLE_MSG = 'Zend_Session is not marked as readable.';


    /**
     * namespaceIsset() - check to see if a namespace or a variable within a namespace is set
     *
     * @param string $namespace
     * @param string $name
     * @return bool
     */
    static protected function _namespaceIsset($namespace, $name = null)
    {
        if (self::$_readable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_READABLE_MSG);
        }

        $return_value = null;

        if ($name === null) {
            return ( isset($_SESSION[$namespace]) || isset(self::$_expiringData[$namespace]) );
        } else {
            return ( isset($_SESSION[$namespace][$name]) || isset(self::$_expiringData[$namespace][$name]) );
        }
    }


    /**
     * namespaceUnset() - unset a namespace or a variable within a namespace
     *
     * @param string $namespace
     * @param string $name
     * @throws Zend_Session_Exception
     * @return void
     */
    static protected function _namespaceUnset($namespace, $name = null)
    {
        if (self::$_writable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_WRITABLE_MSG);
        }

        $name = (string) $name;

        // check to see if the api wanted to remove a var from a namespace or a namespace
        if ($name === '') {
            unset($_SESSION[$namespace]);
            unset(self::$_expiringData[$namespace]);
        } else {
            unset($_SESSION[$namespace][$name]);
            unset(self::$_expiringData[$namespace]);
        }

        // if we remove the last value, remove namespace.
        if (empty($_SESSION[$namespace])) {
            unset($_SESSION[$namespace]);
        }
    }


    /**
     * namespaceGet() - get a variable from a namespace.
     *
     * @param string $namespace
     * @param string $name
     * @return mixed
     */
    static protected function & _namespaceGet($namespace, $name = null)
    {
        if (self::$_readable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_READABLE_MSG);
        }

        $current_data  = (isset($_SESSION[$namespace]) && is_array($_SESSION[$namespace])) ?
            $_SESSION[$namespace] : array();
        $expiring_data = (isset(self::$_expiringData[$namespace]) && is_array(self::$_expiringData[$namespace])) ?
            self::$_expiringData[$namespace] : array();

        $merged_data = array_merge($current_data, $expiring_data);

        if ($name !== null) {
            if (isset($merged_data[$name])) {
                return $merged_data[$name];
            } else {
                $foo = null;
                return $foo;
            }
        } else {
            return $merged_data;
        }
    }

}
