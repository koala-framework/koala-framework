<?php
/**
 * @group Model_Db
 */
class Kwf_Model_Db_FetchTest extends Kwf_Test_TestCase
{
    private $_table;
    private $_dbSelect;
    private $_model;

    public function setUp()
    {
        parent::setUp();
        $this->_table = $this->getMock('Kwf_Model_Db_Table',
            array('select', '_setupMetadata', '_setupPrimaryKey', '_fetch', 'insert'),
            array('db' => new Kwf_Model_Db_TestAdapter()), '', true);

        $this->_dbSelect = $this->getMock('Kwf_Db_Table_Select', array(), array($this->_table));

        $this->_table->expects($this->any())
            ->method('select')
            ->will($this->returnValue($this->_dbSelect));


        $this->_model = new Kwf_Model_Db(array(
            'table' => $this->_table
        ));
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

        $r3 = $this->_model->getRow(1);
        $this->assertTrue($r1 === $r3);
    }

    public function testUniqueRowObjectCreateRow()
    {

        $this->_table->expects($this->any())
            ->method('insert')
            ->will($this->returnValue(2));

        $this->_table->expects($this->any())
            ->method('_fetch')
            ->will($this->returnValue(array(
                    array('id'=>2, 'foo'=>'foo', 'bar'=>null)
                )));

        $r1 = $this->_model->createRow();
        $r1->foo = 'foo';
        $r1->save();

        $r2 = $this->_model->getRow(2);
        $this->assertTrue($r1 === $r2);
    }

    /**
     * Testet ob es eh noch funktioniert wenn in der Zend_Db_Table_Row isset und get
     * überschrieben sind, nur wg. rückwärtskompatibilität notwendig.
     */
    public function testValuesNotInModel()
    {
        $this->_table->expects($this->any())
            ->method('_fetch')
            ->will($this->returnValue(array(
                    array('id'=>1, 'foo'=>'foo', 'bar'=>null)
                )));
        $row = $this->_model->getRows()->current();
        $this->assertTrue(isset($row->foobar)); //foobar kommt aus überschriebenem __isset/__get in row
        $this->assertFalse(isset($row->foobar1));
        $this->assertEquals('foo', $row->foo);
        $this->assertEquals('foobar', $row->foobar);
        $this->assertFalse(isset($row->foobar1));
    }

    /**
     * @expectedException Kwf_Exception
     */
    public function testValuesNotInModelException()
    {
        $this->_table->expects($this->any())
            ->method('_fetch')
            ->will($this->returnValue(array(
                    array('id'=>1, 'foo'=>'foo', 'bar'=>null)
                )));
        $row = $this->_model->getRows()->current();
        $row->foobar1; // wirft Exception
    }
}
