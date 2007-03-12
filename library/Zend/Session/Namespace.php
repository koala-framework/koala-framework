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
 * @version    $Id: Namespace.php 3384 2007-02-13 21:07:21Z gavin $
 * @since      Preview Release 0.2
 */


/**
 * Zend_Session_Namespace
 *
 * @category Zend
 * @package Zend_Session
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Session_Namespace extends Zend_Session_Abstract implements IteratorAggregate
{

    /**
     * used as option to constructor to prevent additional instances to the same namespace
     */
    const SINGLE_INSTANCE = true;

    /**
     * Namespace - which namespace this instance of zend-session is saving-to/getting-from
     *
     * @var string
     */
    protected $_namespace = "Default";

    /**
     * Namespace locking mechanism
     *
     * @var array
     */
    static protected $_namespaceLocks = array();

    /**
     * Single instance namespace array to ensure data security.
     *
     * @var array
     */
    static protected $_singleInstances = array();

    /**
     * __construct() - Returns an instance object bound to a particular, isolated section
     * of the session, identified by $namespace name (defaulting to 'Default').
     * The optional argument $singleInstance will prevent construction of additional
     * instance objects acting as accessors to this $namespace.
     *
     * @param string $namespace       - programmatic name of the requested namespace
     * @param bool $singleInstance    - prevent creation of additional accessor instance objects for this namespace
     * @return void
     */
    public function __construct($namespace = 'Default', $singleInstance = false)
    {
        if ($namespace === '') {
            throw new Zend_Session_Exception('Session namespace must be a non-empty string.');
        }

        if ($namespace[0] == "_") {
            throw new Zend_Session_Exception('Session namespace must not start with an underscore.');
        }

        if (isset(self::$_singleInstances[$namespace])) {
            throw new Zend_Session_Exception("A session namespace object already exists for this namespace ('$namespace'), and no additional accessors (session namespace objects) for this namespace are permitted.");
        }

        if ($singleInstance === true) {
            self::$_singleInstances[$namespace] = true;
        }

        $this->_namespace = $namespace;

        // Process metadata specific only to this namespace.
        Zend_Session::start(true); // attempt auto-start (throws exception if strict option set)

        if (self::$_readable === false) {
            throw new Zend_Session_Exception(self::_THROW_NOT_READABLE_MSG);
        }

        if (!isset($_SESSION['__ZF'])) {
            return; // no further processing needed
        }

        // do not allow write access to namespaces, after stop() or writeClose()
        if (parent::$_writable === true) {
            if (isset($_SESSION['__ZF'][$namespace])) {
    
                // Expire Namespace by Namespace Hop (ENNH)
                if (isset($_SESSION['__ZF'][$namespace]['ENNH'])) {
                    $_SESSION['__ZF'][$namespace]['ENNH']--;
    
                    if ($_SESSION['__ZF'][$namespace]['ENNH'] === 0) {
                        if (isset($_SESSION[$namespace])) {
                            self::$_expiringData[$namespace] = $_SESSION[$namespace];
                            unset($_SESSION[$namespace]);
                        }
                        unset($_SESSION['__ZF'][$namespace]['ENNH']);
                    }
                }
    
                // Expire Namespace Variables by Namespace Hop (ENVNH)
                if (isset($_SESSION['__ZF'][$namespace]['ENVNH'])) {
                    foreach ($_SESSION['__ZF'][$namespace]['ENVNH'] as $variable => $hops) {
                        $_SESSION['__ZF'][$namespace]['ENVNH'][$variable]--;
    
                        if ($_SESSION['__ZF'][$namespace]['ENVNH'][$variable] === 0) {
                            if (isset($_SESSION[$namespace][$variable])) {
                                self::$_expiringData[$namespace][$variable] = $_SESSION[$namespace][$variable];
                                unset($_SESSION[$namespace][$variable]);
                            }
                            unset($_SESSION['__ZF'][$namespace]['ENVNH'][$variable]);
                        }
                    }
                }
            }
    
            if (empty($_SESSION['__ZF'][$namespace])) {
                unset($_SESSION['__ZF'][$namespace]);
            }
    
            if (empty($_SESSION['__ZF'])) {
                unset($_SESSION['__ZF']);
            }
        }
    }


    /**
     * getIterator() - return an iteratable object for use in foreach and the like,
     * this completes the IteratorAggregate interface
     *
     * @return ArrayObject - iteratable container of the namespace contents
     */
    public function getIterator()
    {
        return new ArrayObject(parent::_namespaceGet($this->_namespace));
    }


    /**
     * lock() - mark a session/namespace as readonly
     *
     * @return void
     */
    public function lock()
    {
        self::$_namespaceLocks[$this->_namespace] = true;
    }


    /**
     * unlock() - unmark a session/namespace to enable read & write
     *
     * @return void
     */
    public function unlock()
    {
        unset(self::$_namespaceLocks[$this->_namespace]);
    }


    /**
     * unlockAll() - unmark all session/namespaces to enable read & write
     *
     * @return void
     */
    static public function unlockAll()
    {
        self::$_namespaceLocks = array();
    }


    /**
     * isLocked() - return lock status, true if, and only if, read-only
     *
     * @return bool
     */
    public function isLocked()
    {
        return isset(self::$_namespaceLocks[$this->_namespace]);
    }


    /**
     * unsetAll() - unset all variables in this namespace
     *
     * @return true
     */
    public function unsetAll()
    {
        return parent::_namespaceUnset($this->_namespace);
    }


    /**
     * __get() - method to get a variable in this objects current namespace
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return mixed
     */
    protected function & __get($name)
    {
        if ($name === '') {
            throw new Zend_Session_Exception("The '$name' key must be a non-empty string");
        }

        return parent::_namespaceGet($this->_namespace, $name);
    }


    /**
     * __set() - method to set a variable/value in this objects namespace
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @param mixed $value - value in the <key,value> pair to assign to the $name key
     * @throws Zend_Session_Exception
     * @return true
     */
    protected function __set($name, $value)
    {
        if (isset(self::$_namespaceLocks[$this->_namespace])) {
            throw new Zend_Session_Exception('This session/namespace has been marked as read-only.');
        }

        if ($name === '') {
            throw new Zend_Session_Exception("The '$name' key must be a non-empty string");
        }

        if (parent::$_writable === false) {
            throw new Zend_Session_Exception(parent::_THROW_NOT_WRITABLE_MSG);
        }

        $name = (string) $name;

        $_SESSION[$this->_namespace][$name] = $value;
    }


    /**
     * __isset() - determine if a variable in this objects namespace is set
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return bool
     */
    protected function __isset($name)
    {
        if ($name === '') {
            throw new Zend_Session_Exception("The '$name' key must be a non-empty string");
        }

        return parent::_namespaceIsset($this->_namespace, $name);
    }


    /**
     * __unset() - unset a variable in this objects namespace.
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return true
     */
    protected function __unset($name)
    {
        if ($name === '') {
            throw new Zend_Session_Exception("The '$name' key must be a non-empty string");
        }

        return parent::_namespaceUnset($this->_namespace, $name);
    }


    /**
     * setExpirationSeconds() - expire the namespace, or specific variables after a specified
     * number of seconds
     *
     * @param int $seconds     - expires in this many seconds
     * @param mixed $variables - OPTIONAL list of variables to expire (defaults to all)
     * @throws Zend_Session_Exception
     * @return void
     */
    public function setExpirationSeconds($seconds, $variables = null)
    {
        if (parent::$_writable === false) {
            throw new Zend_Session_Exception(parent::_THROW_NOT_WRITABLE_MSG);
        }

        if ($seconds <= 0) {
            throw new Zend_Session_Exception('Seconds must be positive.');
        }

        if ($variables === null) {

            // apply expiration to entire namespace
            $_SESSION['__ZF'][$this->_namespace]['ENT'] = time() + $seconds;

        } else {

            if (is_string($variables)) {
                $variables = array($variables);
            }

            foreach ($variables as $variable) {
                if (!empty($variable)) {
                    $_SESSION['__ZF'][$this->_namespace]['ENVT'][$variable] = time() + $seconds;
                }
            }
        }
    }


    /**
     * setExpirationHops() - expire the namespace, or specific variables after a specified
     * number of page hops
     *
     * @param int $hops        - how many "hops" (number of subsequent requests) before expiring
     * @param mixed $variables - OPTIONAL list of variables to expire (defaults to all)
     * @param boolean $hopCountOnUsageOnly - OPTIONAL if set, only count a hop/request if this namespace is used
     * @throws Zend_Session_Exception
     * @return void
     */
    public function setExpirationHops($hops, $variables = null, $hopCountOnUsageOnly = false)
    {
        if (parent::$_writable === false) {
            throw new Zend_Session_Exception(parent::_THROW_NOT_WRITABLE_MSG);
        }

        if ($hops <= 0) {
            throw new Zend_Session_Exception('Hops must be positive number.');
        }

        if ($variables === null) {

            // apply expiration to entire namespace
            if ($hopCountOnUsageOnly === false) {
                $_SESSION['__ZF'][$this->_namespace]['ENGH'] = $hops;
            } else {
                $_SESSION['__ZF'][$this->_namespace]['ENNH'] = $hops;
            }

        } else {

            if (is_string($variables)) {
                $variables = array($variables);
            }

            foreach ($variables as $variable) {
                if (!empty($variable)) {
                    if ($hopCountOnUsageOnly === false) {
                        $_SESSION['__ZF'][$this->_namespace]['ENVGH'][$variable] = $hops;
                    } else {
                        $_SESSION['__ZF'][$this->_namespace]['ENVNH'][$variable] = $hops;
                    }
                }
            }
        }
    }

}
