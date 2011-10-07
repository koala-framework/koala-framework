<?php
/**
 * @group Model
 * @group Model_Proxy
 */
class Vps_Model_Proxy_ModelTest extends Vps_Test_TestCase
{
    public function testIsEqual()
    {
        $fnf1 = new Vps_Model_FnF();
        $fnf2 = new Vps_Model_FnF();

        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf1));

        $proxySame = new Vps_Model_Proxy(array('proxyModel' => $fnf1));
        $proxyNotSame = new Vps_Model_Proxy(array('proxyModel' => $fnf2));

        $this->assertEquals(true, $proxy->isEqual($proxySame));
        $this->assertEquals(false, $proxy->isEqual($proxyNotSame));
    }

    public function testPrimary()
    {
        $fnf1 = new Vps_Model_FnF();
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf1));
        $this->assertEquals('id', $proxy->getPrimaryKey());

        $fnf1 = new Vps_Model_FnF(array('primaryKey' => 'user_id'));
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf1));
        $this->assertEquals('user_id', $proxy->getPrimaryKey());
    }

    public function testCreateRow()
    {
        $fnf1 = new Vps_Model_FnF();
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf1));
        $row = $proxy->createRow(array('id' => 3, 'name' => 'Huber'));

        $this->assertEquals('Vps_Model_Proxy_Row', get_class($row));
        $this->assertEquals('Huber', $row->name);
        $this->assertEquals(3, $row->id);
    }

    public function testColumns()
    {
        $fnf1 = new Vps_Model_FnF();
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf1));
        $this->assertEquals(array(), $proxy->getColumns());

        $fnf1 = new Vps_Model_FnF(array('columns' => array('id', 'user_id')));
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf1));
        $this->assertEquals(array('id', 'user_id'), $proxy->getColumns());
    }

    public function testGetRows()
    {
        $fnf = $this->getMock('Vps_Model_FnF', array('getRows'));
        $fnf->expects($this->once())
            ->method('getRows')
            ->with($this->equalTo(null), $this->equalTo(null), $this->equalTo(null), $this->equalTo(null));
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf));
        $rowset = $proxy->getRows();


        $select = new Vps_Model_Select();
        $select->whereId(2);
        $fnf = $this->getMock('Vps_Model_FnF', array('getRows'));
        $fnf->expects($this->once())
            ->method('getRows')
            ->with($this->equalTo($select), $this->equalTo(null), $this->equalTo(null), $this->equalTo(null));
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf));
        $rowset = $proxy->getRows($select);


        $select = new Vps_Model_Select();
        $select->whereId(2);
        $fnf = new Vps_Model_FnF();
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo'),
            array('id' => 18, 'name' => 'bar')
        ));
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf));
        $rowset = $proxy->getRows($select);

        $this->assertEquals('Vps_Model_Proxy_Rowset', get_class($rowset));
        $this->assertEquals('Vps_Model_Proxy_Row', get_class($rowset->current()));
        $this->assertEquals(1, count($rowset));

        $this->assertEquals('foo', $rowset->current()->name);
    }

    public function testCountRows()
    {
        $fnf = new Vps_Model_FnF();
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo'),
            array('id' => 18, 'name' => 'bar'),
            array('id' => 456, 'name' => 'bar2')
        ));
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf));
        $this->assertEquals(3, $proxy->countRows());

        $select = new Vps_Model_Select();
        $select->whereId(2);
        $fnf = new Vps_Model_FnF();
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo'),
            array('id' => 18, 'name' => 'bar'),
            array('id' => 456, 'name' => 'bar2')
        ));
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf));
        $this->assertEquals(1, $proxy->countRows($select));
    }

    public function testUniqueRowObject()
    {
        $fnf = new Vps_Model_FnF();
        $fnf->setData(array(
            array('id' => 2, 'name' => 'foo'),
            array('id' => 18, 'name' => 'bar'),
            array('id' => 456, 'name' => 'bar2')
        ));
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf));

        $r1 = $proxy->getRow(2);
        $r2 = $proxy->getRow(2);
        $this->assertTrue($r1 === $r2);
    }

    public function testUniqueRowObjectCreateRow()
    {
        $fnf = new Vps_Model_FnF();
        $fnf->setData(array(
            array('id' => 1, 'name' => 'foo'),
        ));
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf));

        $r1 = $proxy->createRow();
        $newId = $r1->save();
        $this->assertEquals(2, $newId);
        $r2 = $proxy->getRow(2);

        $r1->name = 'foo1';
        $this->assertEquals($r2->name, 'foo1');

        $this->assertTrue($r1 === $r2);
    }

    public function testDefaultValues()
    {
        $fnf = new Vps_Model_FnF(array(
            'default' => array('foo'=>'defaultFoo')
        ));
        $proxy = new Vps_Model_Proxy(array('proxyModel' => $fnf));

        $row = $proxy->createRow();
        $this->assertEquals('defaultFoo', $row->foo);
    }
}
