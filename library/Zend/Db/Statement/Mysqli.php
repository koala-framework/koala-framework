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
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Db_Statement
 */
require_once 'Zend/Db/Statement.php';

/**
 * Extends for Mysqli
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Statement_Mysqli extends Zend_Db_Statement
{

    /**
     * The mysqli_stmt object.
     */
    protected $_stmt;

    /**
     * Column names.
     */
    protected $_keys;

    /**
     * Fetched result values.
     */
    protected $_values;

    /**
     * Closes the cursor, allowing the statement to be executed again.
     *
     * @return void
     */
    public function closeCursor()
    {
        $this->_stmt->close();
    }

    /**
     * Returns the number of columns in the result set.
     *
     * @return field count.
     */
    public function columnCount()
    {
        return $this->_meta->field_count;
    }

    /**
     * Retrieves an error code, if any, from the statement.
     *
     * @return error code.
     */
    public function errorCode()
    {
        return substr($this->_stmt->sqlstate, 0, 5);
    }

    /**
     * Retrieves an array of error information, if any, from the statement.
     *
     * @return array
     */
    public function errorInfo()
    {
        return array(
            substr($this->_stmt->sqlstate, 5),
            $this->_stmt->errno,
            $this->_stmt->error,
        );
    }

    /**
     * Executes a prepared statement.
     *
     * @param $params
     * @return void
     */
    public function execute($params = null)
    {
        // prepare for mysqli
        $sql = $this->_joinSql();
        $mysqli = $this->_db->getMysqli();
        $this->_stmt = $mysqli->prepare($sql);

        if (! $this->_stmt) {
            throw new Zend_Db_Statement_Exception($mysqli->error);
        }

        // retain metadata
        $this->_meta = $this->_stmt->result_metadata();

        // get the column names that will result
        $this->_keys = array();
        foreach ($this->_meta->fetch_fields() as $col) {
            $this->_keys[] = $col->name;
        }

        // set up a binding space for result variables
        $this->_values = array_fill(0, count($this->_keys), null);

        // set up references to the result binding space.
        // just passing $this->_values in the call_user_func_array()
        // below won't work, you need references.
        $refs = array();
        foreach ($this->_values as $i => &$f) {
            $refs[$i] = &$f;
        }

        // bind to the result variables
        call_user_func_array(
            array($this->_stmt, 'bind_result'),
            $this->_values
        );

        // execute the statement
        $this->_stmt->execute();
    }


    /**
     * Fetches a row from the result set.
     *
     * @param $style
     * @param $cursor
     * @param $offset
     * @return $data
     * @throws Zend_Db_Statement_Exception
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        // fetch the next result
        $ok = $this->_stmt->fetch();
        if (! $ok) {
            return false;
        }

        // make sure we have a fetch mode
        if ($style === null) {
            $style = $this->_fetchMode;
        }

        // dereference the result values, otherwise things like fetchAll()
        // return the same values for every entry (because of the reference).
        $values = array();
        foreach ($this->_values as $key => $val) {
            $values[] = $val;
        }

        // bind back to external references
        foreach ($this->_bindColumn as $key => &$val) {
            if (is_integer($key)) {
                // bind by column position
                // note that vals are 0-based, but cols are 1-based
                $val = $values[$key-1];
            } else {
                // bind by column name
                $i = array_search($key, $this->_keys);
                $val = $values[$i];
            }
        }

        // return based on fetch mode
        $data = false;
        switch ($style) {
            case Zend_Db::FETCH_NUM:
                $data = $values;
                break;
            case Zend_Db::FETCH_ASSOC:
                $data = array_combine($this->_keys, $values);
                break;
            case Zend_Db::FETCH_BOTH:
                $assoc = array_combine($this->_keys, $values);
                $data = array_merge($values, $assoc);
                break;
            case Zend_Db::FETCH_OBJ:
                $data = (object) array_combine($this->_keys, $values);
                break;
            default:
                throw new Zend_Db_Statement_Exception("invalid fetch mode specified");
                break;
        }

        // done
        return $data;
    }


    /**
     * Returns the number of rows that were affected by the execution of an SQL statement.
     * @return num_rows
     */
    public function rowCount()
    {
        return $this->_meta->num_rows;
    }
}
