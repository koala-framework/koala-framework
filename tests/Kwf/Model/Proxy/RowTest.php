<?php
/**
 * @group Model
 * @group Model_Proxy
 */
class Vps_Model_Proxy_RowTest extends Vps_Test_TestCase
{
    /**
     * @expectedException Vps_Exception
     */
    public function testUnsetFalse()
    {
        $fnf = new Vps_Model_FnF(array(
            'data' => array(
                array('id' => 4, 'names' => 'foo')
            ),
            'columns' => array('id', 'names')
        ));
        $model = new Vps_Model_Proxy(array('proxyModel' => $fnf));

        $row = $model->getRow(4);
        unset($row->namesFOO);
    }

    public function testToArray()
    {
        $fnf = new Vps_Model_FnF(array(
            'data' => array(
                array('id' => 4, 'names' => 'foo')
            ),
            'columns' => array('id', 'names')
        ));
        $model = new Vps_Model_Proxy(array('proxyModel' => $fnf));

        $row = $model->getRow(4);
        $this->assertEquals(array('id' => 4, 'names' => 'foo'), $row->toArray());
    }

    public function testIssetWithSiblings()
    {
        $fnf = new Vps_Model_FnF(array(
            'columns' => array('id', 'name', 'data'),
            'data' => array(
                array('id' => 4, 'name' => 'foo', 'data' => json_encode(array('name1'=>'foo1'))),
            ),
            'siblingModels' => array(
                new Vps_Model_Field(array(
                    'columns' => array('name1'),
                    'fieldName' => 'data'
                ))
            )
        ));
        $model = new Vps_Model_Proxy(array('proxyModel' => $fnf));

        $this->assertEquals(array('id', 'name', 'data'), $fnf->getOwnColumns());
        $this->assertEquals(array('id', 'name', 'data', 'name1'), $fnf->getColumns());
        $this->assertTrue($fnf->hasColumn('id'));
        $this->assertTrue($fnf->hasColumn('name'));
        $this->assertTrue($fnf->hasColumn('name1'));

        $this->assertEquals(array('id', 'name', 'data', 'name1'), $model->getOwnColumns());
        $this->assertEquals(array('id', 'name', 'data', 'name1'), $model->getColumns());
        $this->assertTrue($model->hasColumn('id'));
        $this->assertTrue($model->hasColumn('name'));
        $this->assertTrue($model->hasColumn('name1'));

        $row = $model->getRow(4);

        // isset
        $this->assertTrue(isset($row->name));
        $this->assertTrue(isset($row->name1));
        $this->assertFalse(isset($row->bar));

        // get
        $this->assertEquals(4, $row->id);
        $this->assertEquals('foo', $row->name);
        $this->assertEquals('foo1', $row->name1);

        // set
        $row->name = 'bar';
        $this->assertEquals('bar', $row->name);
    }

    public function testIssetWithProxySiblings()
    {
        $fnf = new Vps_Model_FnF(array(
            'columns' => array('id', 'name', 'data'),
            'data' => array(
                array('id' => 4, 'name' => 'foo', 'data' => json_encode(array('name1'=>'foo1'))),
            )
        ));
        $model = new Vps_Model_Proxy(array(
            'proxyModel' => $fnf,
            'siblingModels' => array(
                new Vps_Model_Field(array(
                    'fieldName' => 'data',
                    'columns' => array('name1')
                ))
            )
        ));

        $this->assertEquals(array('id', 'name', 'data'), $fnf->getColumns());
        $this->assertTrue($fnf->hasColumn('id'));
        $this->assertTrue($fnf->hasColumn('name'));
        $this->assertFalse($fnf->hasColumn('name1'));

        $this->assertEquals(array('id', 'name', 'data', 'name1'), $model->getColumns());
        $this->assertTrue($model->hasColumn('id'));
        $this->assertTrue($model->hasColumn('name'));

        $this->assertTrue($model->hasColumn('name1'));

        $row = $model->getRow(4);

        // isset
        $this->assertTrue(isset($row->name));
        $this->assertFalse(isset($row->bar));
        $this->assertTrue(isset($row->name1));

        // get
        $this->assertEquals(4, $row->id);
        $this->assertEquals('foo', $row->name);
        $this->assertEquals('foo1', $row->name1);

        // set
        $row->name = 'bar';
        $this->assertEquals('bar', $row->name);
    }
}