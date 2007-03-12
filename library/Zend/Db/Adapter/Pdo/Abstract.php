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
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Db_Adapter_Abstract */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 * Class for connecting to SQL databases and performing common operations using PDO.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Adapter_Pdo_Abstract extends Zend_Db_Adapter_Abstract
{

    /**
     * Creates a PDO DSN for the adapter from $this->_config settings.
     *
     * @return string
     */
    protected function _dsn()
    {
        // baseline of DSN parts
        $dsn = $this->_config;

        // don't pass the username and password in the DSN
        unset($dsn['username']);
        unset($dsn['password']);

        // use all remaining parts in the DSN
        foreach ($dsn as $key => $val) {
            $dsn[$key] = "$key=$val";
        }

        return $this->_pdoType . ':' . implode(';', $dsn);
    }

    /**
     * Creates a PDO object and connects to the database.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _connect()
    {
        // if we already have a PDO object, no need to re-connect.
        if ($this->_connection) {
            return;
        }

        // check for PDO extension
        if (!extension_loaded('pdo')) {
            throw new Zend_Db_Adapter_Exception('The PDO extension is required for this adapter but not loaded');
        }

        // check the PDO driver is available
        if (!in_array($this->_pdoType, PDO::getAvailableDrivers())) {
            throw new Zend_Db_Adapter_Exception('The ' . $this->_pdoType . ' driver is not currently installed');
        }

        // create PDO connection
        $q = $this->_profiler->queryStart('connect', Zend_Db_Profiler::CONNECT);

        try {
            $this->_connection = new PDO(
                $this->_dsn(),
                $this->_config['username'],
                $this->_config['password']
            );

            $this->_profiler->queryEnd($q);

            // force names to lower case
            $this->_connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);

            // always use exceptions.
            $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            /** @todo Are there other portability attribs to consider? */
        } catch (PDOException $e) {
            throw new Zend_Db_Adapter_Exception($e->getMessage(), $e->getCode());
        }

    }

    /**
     * Prepares an SQL statement.
     *
     * @param string $sql The SQL statement with placeholders.
     * @param array $bind An array of data to bind to the placeholders.
     * @return PDOStatement
     */
    public function prepare($sql)
    {
        $this->_connect();
        return $this->_connection->prepare($sql);
    }

    /**
     * Gets the last inserted ID.
     *
     * @param  string $sequenceName  Table or sequence name needed for some PDO drivers
     * @return integer
     */
    public function lastInsertId($sequenceName = null)
    {
        $this->_connect();
        return $this->_connection->lastInsertId();
    }

    /**
     * Special handling for PDO query().
     * All bind parameter names must begin with ':'
     *
     * @param string|Zend_Db_Select $sql The SQL statement with placeholders.
     * @param array $bind An array of data to bind to the placeholders.
     * @return Zend_Db_Pdo_Statement
     */
    public function query($sql, $bind = array())
    {
        if (is_array($bind)) {
            foreach ($bind as $name => $value) {
                if (!is_int($name) && !preg_match('/^:/', $name)) {
                    $newName = ":$name";
                    unset($bind[$name]);
                    $bind[$newName] = $value;
                }
            }
        }
        return parent::query($sql, $bind);
    }

    /**
     * Begin a transaction.
     */
    protected function _beginTransaction()
    {
        $this->_connection->beginTransaction();
    }

    /**
     * Commit a transaction.
     */
    protected function _commit()
    {
        $this->_connection->commit();
    }

    /**
     * Roll-back a transaction.
     */
    protected function _rollBack() {
        $this->_connection->rollBack();
    }

    /**
     * Quote a raw string.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    protected function _quote($value)
    {
        return $this->_connection->quote($value);
    }

    /**
     * Set the PDO fetch mode.
     *
     * @todo Support FETCH_CLASS and FETCH_INTO.
     *
     * @param int $mode A PDO fetch mode.
     * @return void
     */
    public function setFetchMode($mode)
    {
        switch ($mode) {
            case PDO::FETCH_LAZY:
            case PDO::FETCH_ASSOC:
            case PDO::FETCH_NUM:
            case PDO::FETCH_BOTH:
            case PDO::FETCH_NAMED:
            case PDO::FETCH_OBJ:
                $this->_fetchMode = $mode;
                break;
            default:
                throw new Zend_Db_Adapter_Exception('Invalid fetch mode specified');
                break;
        }
    }

}

