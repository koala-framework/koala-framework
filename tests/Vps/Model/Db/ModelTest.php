<?php
/**
 * @group Model_Db
 */
class Vps_Model_Db_ModelTest extends PHPUnit_Framework_TestCase
{
    private $_table;
    private $_dbSelect;
    private $_model;

    public function setUp()
    {
        $this->_table = $this->getMock('Vps_Model_Db_Table',
            array('select', '_setupMetadata', '_setupPrimaryKey', 'fetchAll', 'delete', 'save'),
            array('db' => new Vps_Model_Db_TestAdapter()), '', true);

        $this->_dbSelect = $this->getMock('Vps_Db_Table_Select', array(), array($this->_table));

        $this->_table->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->_dbSelect));

        $this->_model = new Vps_Model_Db(array(
            'table' => $this->_table,
            'default' => array('foo' => 'defaultFoo')
        ));
    }

    public function testFetchAll()
    {
        $this->_table->expects($this->once())
                  ->method('fetchAll');
        $this->_model->fetchAll();
    }

    public function testDelete()
    {
        $this->_table->expects($this->once())
                  ->method('delete')
                  ->with($this->equalTo(array()));
        $this->_model->deleteRows(array());
    }

    public function testDeleteWhereEquals()
    {
        $this->_table->expects($this->once())
            ->method('delete')
            ->with($this->equalTo(array(
                "(testtable.foo = 'bar')",
                "(testtable.bar = 1)"
            ))
        );
        $select = $this->_model->select()
            ->whereEquals('foo', 'bar')
            ->whereEquals('bar', 1);
        $this->_model->deleteRows($select);
    }

    public function testDeleteWhereExpr()
    {
        $this->_table->expects($this->once())
            ->method('delete')
            ->with($this->equalTo(array(
                "(testtable.foo > 1)"
            ))
        );
        $select = $this->_model->select()->where(
            new Vps_Model_Select_Expr_Higher('foo', 1)
        );
        $this->_model->deleteRows($select);
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testDeleteException()
    {
        $select = $this->_model->select()->join('foo');
        $this->_model->deleteRows($select);
    }

    public function testSelectWhereEquals()
    {
        $this->_dbSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo('testtable.foo = ?'), $this->equalTo(1));
        $select = $this->_model->select()
                    ->whereEquals('foo', 1);
        $this->_table->expects($this->once())
                  ->method('fetchAll')
                  ->with($this->equalTo($this->_dbSelect));
        $this->_model->fetchAll($select);
    }

    public function testSelectWhereId()
    {
        $this->_dbSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo('testtable.id = ?'), $this->equalTo(1));
        $this->_table->expects($this->once())
                  ->method('fetchAll')
                  ->with($this->equalTo($this->_dbSelect));

        $select = $this->_model->select()
                    ->whereId(1);
        $this->_model->fetchAll($select);
    }

    public function testSelectOrder()
    {
        $this->_dbSelect->expects($this->once())
            ->method('order')
            ->with($this->equalTo('testtable.bar ASC'));
        $select = $this->_model->select()
            ->order('bar', 'ASC');
        $this->_table->expects($this->once())
                  ->method('fetchAll')
                  ->with($this->equalTo($this->_dbSelect));
        $this->_model->fetchAll($select);
    }

    public function testSelectOrder2()
    {
        $this->_dbSelect->expects($this->once())
            ->method('order')
            ->with($this->equalTo('testtable.bar ASC'));
        $select = $this->_model->select()
            ->order(array('field'=>'bar', 'direction'=>'ASC'));
        $this->_table->expects($this->once())
                  ->method('fetchAll')
                  ->with($this->equalTo($this->_dbSelect));
        $this->_model->fetchAll($select);
    }

    public function testSelectWhereEqualsArray1()
    {
        $this->_dbSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo('testtable.id IN (1, 2)'));
        $select = $this->_model->select()
            ->whereEquals('id', array(1, 2));
        $this->_table->expects($this->once())
                  ->method('fetchAll')
                  ->with($this->equalTo($this->_dbSelect));
        $this->_model->fetchAll($select);
    }

    public function testSelectWhereEqualsArray2()
    {
        $this->_dbSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo("testtable.foo IN ('str1', 'str2')"));
        $select = $this->_model->select()
            ->whereEquals('foo', array('str1', 'str2'));
        $this->_model->fetchAll($select);
    }

    public function testSelectWhere()
    {
        $this->_dbSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo("foo = ?"), $this->equalTo(1));
        $select = $this->_model->select()
            ->where('foo = ?', 1);
        $this->_model->fetchAll($select);
    }

    public function testJoin()
    {
        $this->_dbSelect->expects($this->once())
            ->method('join')
            ->with($this->equalTo("foo"), $this->equalTo('foo.bar=blub.bar'));
        $select = $this->_model->select()
            ->join('foo', 'foo.bar=blub.bar');
        $this->_model->fetchAll($select);
    }

    public function testWithoutSelect()
    {
        $this->_dbSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo("foo = 'bar'"));

        $this->_dbSelect->expects($this->once())
            ->method('order')
            ->with($this->equalTo('testtable.orderKey ASC'));

        $this->_dbSelect->expects($this->once())
            ->method('limit')
            ->with($this->equalTo(10), $this->equalTo(5));

        $this->_table->expects($this->once())
                  ->method('fetchAll')
                  ->with($this->equalTo($this->_dbSelect));

        $this->_model->fetchAll("foo = 'bar'", 'orderKey', 10, 5);
    }

    public function testOrderRand()
    {
        $this->_dbSelect->expects($this->once())
            ->method('order')
            ->with($this->equalTo('RAND()'));
        $select = $this->_model->select()
            ->order(Vps_Model_Select::ORDER_RAND);
        $this->_model->fetchAll($select);
    }

    public function testNull()
    {
        $this->_dbSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo('ISNULL(testtable.foo)'));
        $select = $this->_model->select()
            ->whereNull('foo');
        $this->_model->fetchAll($select);
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testNullWithoutSelect()
    {
        $this->_model->fetchAll(array('foo = ?'=>null));
    }

    public function testSelectWhereNotEquals1()
    {
        $this->_dbSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo('testtable.id != ?'), $this->equalTo(1));
        $select = $this->_model->select()
            ->whereNotEquals('id', 1);
        $this->_table->expects($this->once())
                  ->method('fetchAll')
                  ->with($this->equalTo($this->_dbSelect));
        $this->_model->fetchAll($select);
    }

    public function testSelectWhereNotEquals2()
    {
        $this->_dbSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo("testtable.foo != ?"), $this->equalTo('bar'));
        $select = $this->_model->select()
            ->whereNotEquals('foo', 'bar');
        $this->_table->expects($this->once())
                  ->method('fetchAll')
                  ->with($this->equalTo($this->_dbSelect));
        $this->_model->fetchAll($select);
    }

    public function testSelectWhereNotEqualsArray1()
    {
        $this->_dbSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo('testtable.id NOT IN (1, 2)'));
        $select = $this->_model->select()
            ->whereNotEquals('id', array(1, 2));
        $this->_table->expects($this->once())
                  ->method('fetchAll')
                  ->with($this->equalTo($this->_dbSelect));
        $this->_model->fetchAll($select);
    }

    public function testDefaultValues()
    {
        $row = $this->_model->createRow();
        $this->assertEquals('defaultFoo', $row->foo);
    }

    public function testIsSet()
    {
        $row = $this->_model->createRow();
        $this->assertTrue(isset($row->bar));
    }
}
