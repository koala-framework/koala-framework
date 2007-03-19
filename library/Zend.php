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
 * @package    Zend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Zend.php 3900 2007-03-13 18:51:49Z bkarwin $
 */

/**
 * Zend_Exception
 */
require_once 'Zend/Exception.php';

/**
 * Utility class for common functions.
 *
 * @deprecated Since Zend Framework 0.9.0.
 *
 * @category   Zend
 * @package    Zend
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
final class Zend
{
    /**
     * Zend Framework version identification - see compareVersion()
     *
     * @deprecated Since 0.9.0 -- use Zend_Version::VERSION instead.
     */
    const VERSION = '0.9.0dev';

    /**
     * Object registry provides storage for shared objects
     *
     * @var Zend_Registry
     */
    static private $_registry = null;

    /**
     * Loads a class from a PHP file.  The filename must be formatted
     * as "$class.php".
     *
     * If $dirs is a string or an array, it will search the directories
     * in the order supplied, and attempt to load the first matching file.
     *
     * If $dirs is null, it will split the class name at underscores to
     * generate a path hierarchy (e.g., "Zend_Example_Class" will map
     * to "Zend/Example/Class.php").
     *
     * If the file was not found in the $dirs, or if no $dirs were specified,
     * it will attempt to load it from PHP's include_path.
     *
     * @param string $class      - The full class name of a Zend component.
     * @param string|array $dirs - OPTIONAL either a path or array of paths to search
     * @throws Zend_Exception
     * @return void
     *
     * @deprecated Since 0.9.0 -- Use Zend_Loader::loadClass() instead.
     */
    static public function loadClass($class, $dirs = null)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . " deprecated since 0.9.0, use Zend_Loader::loadClass() instead");
        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass($class, $dirs);
    }

    /**
     * Loads an interface from a PHP file
     *
     * @deprecated Since 0.6
     */
    static public function loadInterface($class, $dirs = null)
    {
        throw new Zend_Exception(__FUNCTION__ . " has been removed. Please use require_once().");
    }

    /**
     * Loads a PHP file.  This is a wrapper for PHP's include() function.
     *
     * $filename must be the complete filename, including any
     * extension such as ".php".  Note that a security check is performed that
     * does not permit extended characters in the filename.  This method is
     * intended for loading Zend Framework files.
     *
     * If $dirs is a string or an array, it will search the directories
     * in the order supplied, and attempt to load the first matching file.
     *
     * If the file was not found in the $dirs, or if no $dirs were specified,
     * it will attempt to load it from PHP's include_path.
     *
     * If $once is TRUE, it will use include_once() instead of include().
     *
     * @param  string        $filename
     * @param  string|array  $dirs - OPTIONAL either a path or array of paths to search
     * @param  boolean       $once
     * @throws Zend_Exception
     * @return mixed
     *
     * @deprecated Since 0.9.0 -- Use Zend_Loader::loadFile() instead.
     */
    static public function loadFile($filename, $dirs = null, $once = false)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . " deprecated since 0.9.0, use Zend_Loader::loadFile() instead");
        require_once 'Zend/Loader.php';
        Zend_Loader::loadFile($filename, $dirs, $once);
    }

    /**
     * Returns TRUE if the $filename is readable, or FALSE otherwise.  This
     * function uses the PHP include_path, where PHP's is_readable() does not.
     *
     * @param string $filename
     * @return boolean
     *
     * @deprecated Since 0.9.0 -- Use Zend_Loader::isReadable() instead.
     */
    static public function isReadable($filename)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . " deprecated since 0.9.0, use Zend_Loader::isReadable() instead");
        require_once 'Zend/Loader.php';
        return Zend_Loader::isReadable($filename);
    }

    /**
     * Return a new exception
     *
     * Loads an exception class as specified by $class, and then passes the
     * message and code arguments to the Exception's constructor, returning the
     * new Exception object.
     *
     * If the exception created is not a true Exception, throws a Zend_Exception
     * indicating an invalid exception class was passed.
     *
     * Usage:
     * <code>
     *     throw Zend::exception('Some_Exception', 'exception message');
     * </code>
     *
     * @param string $class
     * @param string $message Defaults to empty string
     * @param int $code Defaults to 0
     * @return Exception
     * @throws Zend_Exception when invalid exception class passed
     *
     * @deprecated Since 0.6.1
     */
    static public function exception($class, $message = '', $code = 0)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . " deprecated since 0.6.1");

        $class = (string) $class;

        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass($class);

        $exception = new $class($message, $code);

        if (!$exception instanceof Exception) {
            throw new Zend_Exception('Invalid exception class used in Zend::exception()');
        }

        return $exception;
    }

    /**
     * offsetSet stores $newval at key $index
     *
     * @param mixed $index  index to set
     * @param $newval new value to store at offset $index
     * @return  void
     *
     * @deprecated Since 0.9.0 -- Use Zend_Registry::set() instead.
     */
    static public function register($index, $newval)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . " deprecated since 0.9.0, use Zend_Registry::set() instead");
        require_once 'Zend/Registry.php';
        Zend_Registry::set($index, $newval);
    }

    /**
     * registry() retrieves the value stored at an index.
     *
     * If the $index argument is NULL or not specified,
     * this method returns the registry object (iterable).
     *
     * @see     register()
     * @param   string      $index The name for the value.
     * @throws  Zend_Registry_Exception
     * @return  mixed       The registered value for $index.
     *
     * @deprecated Since 0.9.0 -- Use Zend_Registry::get() instead.
     */
    static public function registry($index = null)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . " deprecated since 0.9.0, use Zend_Registry::get() instead");
        require_once 'Zend/Registry.php';
        Zend_Registry::get($index);
    }

    /**
     * Returns TRUE if the $index is a named value in the
     * registry, or FALSE if $index was not found in the registry.
     *
     * @param  string $index
     * @return boolean
     *
     * @deprecated Since 0.9.0 -- Use Zend_Registry::isRegistered() instead.
     */
    static public function isRegistered($index)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . " deprecated since 0.9.0, use Zend_Registry::isRegistered() instead");
        require_once 'Zend/Registry.php';
        return Zend_Registry::isRegistered($index);
    }

    /**
     * Initialize the registry. Invoking this method more than once will generate an exception.
     *
     * @param mixed $registry - Either a name of the registry class (Zend_Registry, or a subclass)
     *                          or an instance of Zend_Registry (or subclass)
     * @return Zend_Registry
     *
     * @deprecated Since 0.9.0 -- Use Zend_Registry::setClassName() instead.
     */
    static public function initRegistry($registry = 'Zend_Registry')
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . " deprecated since 0.9.0, use Zend_Registry::setClassName() instead");
        require_once 'Zend/Registry.php';
        Zend_Registry::setClassName($registry);
        return Zend_Registry::getInstance();
    }

    /**
     * primarily for tearDown() in unit tests
     *
     * @deprecated Since 0.9.0 -- Use Zend_Registry::_unsetInstance() instead.
     */
    static public function __unsetRegistry()
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . " deprecated since 0.9.0, use Zend_Registry::_unsetInstance() instead");
        require_once 'Zend/Registry.php';
        Zend_Registry::_unsetInstance();
    }

    /**
     * Debug helper function.  This is a wrapper for var_dump() that adds
     * the <pre /> tags, cleans up newlines and indents, and runs
     * htmlentities() before output.
     *
     * @param  mixed  $var The variable to dump.
     * @param  string $label An optional label.
     * @return string
     *
     * @deprecated since 0.9.0
     */
    static public function dump($var, $label=null, $echo=true)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . " deprecated since 0.9.0, use Zend_Debug::dump() instead");
        require_once 'Zend/Debug.php';
        return Zend_Debug::dump($var, $label, $echo);
    }

    /**
     * Compare the specified ZF $version with the current Zend::VERSION of the ZF.
     *
     * @param  string  $version  A version identifier for the ZF (e.g. "0.7.1")
     * @return boolean    -1 if the $version is older, 0 if they are the same, and +1 if $version is newer
     *
     * @deprecated Since 0.9.0 -- Use Zend_Version::compareVersion() instead.
     */
    static public function compareVersion($version)
    {
        trigger_error(__CLASS__ . "::" . __FUNCTION__ . " deprecated since 0.9.0, use Zend_Version::compareVersion() instead");
        require_once 'Zend/Version.php';
        return Zend_Version::compareVersion($version);
    }

}
