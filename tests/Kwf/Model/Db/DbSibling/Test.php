<?php
/**
 * @group Model_Db_Sibling
 */
class Vps_Model_Db_DbSibling_Test extends Vps_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_db = $this->getMock('Vps_Model_Db_TestAdapter',
            array('query'));
        $this->_table = $this->getMock('Vps_Model_Db_DbSibling_MasterTable',
            array('select', '_setupMetadata', '_setupPrimaryKey'),
            array('db' => $this->_db), '', true);

        $this->_dbSelect = $this->getMock('Vps_Db_Table_Select', array(), array($this->_table));

        $this->_table->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->_dbSelect));


        $this->_model = new Vps_Model_Db_DbSibling_MasterModel(array(
            'table' => $this->_table
        ));

        $this->_stmt = $this->getMock('Zend_Db_Statement',
            array('fetchAll', 'closeCursor', 'columnCount', 'errorCode',
                    'errorInfo', 'fetch', 'nextRowset', 'rowCount'),
            array($this->_db, ''),
            '', false);
    }
    public function testIt()
    {
        $this->_dbSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo('sibling.baz = ?'), $this->equalTo(1));
        $this->_dbSelect->expects($this->once())
            ->method('joinLeft')
            ->with($this->equalTo('sibling'), 'master.id = sibling.master_id');

        $this->_db->expects($this->once())
                ->method('query')
                ->with($this->equalTo($this->_dbSelect))
                ->will($this->returnValue($this->_stmt));

        $this->_stmt->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue(array(array('id'=>1, 'foo'=>'a', 'bar'=>'b'))));


        $select = $this->_model->select()
                    ->whereEquals('baz', 1);

        $rows = $this->_model->fetchAll($select);
        $this->assertEquals(1, $rows->count());
        $this->assertEquals('1', $rows->current()->id);
        $this->assertEquals('a', $rows->current()->foo);
        $this->assertEquals('b', $rows->current()->bar);
    }
}
