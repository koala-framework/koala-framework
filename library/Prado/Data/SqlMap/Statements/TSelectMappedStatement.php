<?php
/**
 * TSelectMappedStatement class.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2007 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: TSelectMappedStatement.php 1568 2006-12-09 09:17:22Z wei $
 * @package System.Data.SqlMap.Statements
 */

/**
 * TSelectMappedStatment class.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id: TSelectMappedStatement.php 1568 2006-12-09 09:17:22Z wei $
 * @package System.Data.SqlMap.Statements
 * @since 3.1
 */
class TSelectMappedStatement extends TMappedStatement
{
	public function executeInsert($connection, $parameter)
	{
		throw new TSqlMapExecutionException(
				'sqlmap_cannot_execute_insert', get_class($this), $this->getID());
	}

	public function executeUpdate($connection, $parameter)
	{
		throw new TSqlMapExecutionException(
				'sqlmap_cannot_execute_update', get_class($this), $this->getID());
	}

}

?>