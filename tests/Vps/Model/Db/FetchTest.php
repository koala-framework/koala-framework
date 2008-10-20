<?php
/**
 * @group Model_Db
 */
class Vps_Model_Db_FetchTest extends PHPUnit_Framework_TestCase
{
    private $_table;
    private $_dbSelect;
    private $_model;

    public function setUp()
    {
        $this->_table = $this->getMock('Vps_Db_Table_Abstract',
            array('select', '_setupMetadata', '_setupPrimaryKey', '_fetch', 'info'),
            array('db' => new Vps_Model_Db_TestAdapter()), '', true);

        $this->_dbSelect = $this->getMock('Vps_Db_Table_Select', array(), array(), '', false);

        $this->_table->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->_dbSelect));

        $this->_table->expects($this->any())
            ->method('info')
            ->will($this->returnCallback(array($this, 'tableInfoCallback')));

        $this->_model = new Vps_Model_Db(array(
            'table' => $this->_table
        ));
    }

    public function tableInfoCallback($type = null)
    {
        if ($type == 'name') return 'testtable';
        if ($type == 'primary') return array('id');
        if ($type == 'cols') return array('id', 'foo', 'bar');
    }

    public function testGetRowsEmpty()
    {
        $this->_table->expects($this->once())
            ->method('_fetch')
            ->will($this->returnValue(array()));
        $this->assertEquals(0, $this->_model->getRows()->count());
    }

    public function testGetRows()
    {
        $this->_table->expects($this->exactly(3))
            ->method('_fetch')
            ->will($this->returnValue(array(
                    array('id'=>1, 'foo'=>'foo', 'bar'=>null)
                )));
        $this->assertEquals(1, $this->_model->getRows()->count());
        $this->assertEquals('foo', $this->_model->getRows()->current()->foo);
        $this->assertEquals(null, $this->_model->getRows()->current()->bar);
    }

    public function testUniqueRowObject()
    {
        $this->markTestIncomplete();

        $this->_table->expects($this->any())
            ->method('_fetch')
            ->will($this->returnValue(array(
                    array('id'=>1, 'foo'=>'', 'bar'=>null)
                )));

        $r1 = $this->_model->getRows()->current();
        $r2 = $this->_model->getRows()->current();
        
        $this->assertEquals($r2->foo, '');
        $r1->foo = 'foo';
        $this->assertEquals($r2->foo, 'foo');
        $this->assertTrue($r1 === $r2);

        $r3 = $this->_model->getRow();
        $this->assertTrue($r1 === $r3);
    }
}
