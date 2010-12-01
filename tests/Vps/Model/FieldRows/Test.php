<?php
/**
 * @group Model
 * @group Model_FieldRows
 */
class Vps_Model_FieldRows_Test extends Vps_Test_TestCase
{
    public function testFnFFieldRows()
    {
        $model = new Vps_Model_FnF(array(
            'columns' => array(),
            'data'=>array(array('id'=>1, 'foo'=>'bar', 'data'=>serialize(
            array(
                'autoId'=>2,
                'data'=>array(
                    array('id'=>1, 'blub'=>'blub1', 'foo'=>'foo'),
                    array('id'=>2, 'blub'=>'blub2')
                )
            )))),
            'dependentModels' => array('Child'=>new Vps_Model_FieldRows(array('fieldName'=>'data')))
        ));


        $row = $model->getRow(1);
        $this->assertEquals($row->foo, 'bar');
        $rows = $row->getChildRows('Child');
        $this->assertEquals(count($rows), 2);
        $this->assertEquals($rows->current()->blub, 'blub1');
        $this->assertEquals($rows->current()->id, 1);
        $rows->current()->foo = 'foo';
        $rows->current()->save();
        $row->blub1 = 'blub1';
        $row->save();

        $this->assertEquals($model->getData(), array(
            array('id'=>1, 'foo'=>'bar', 'data'=>serialize(
            array(
                'autoId'=>2,
                'data'=>array(
                    array('id'=>1, 'blub'=>'blub1', 'foo'=>'foo'),
                    array('id'=>2, 'blub'=>'blub2')
                )
            )),
            'blub1'=>'blub1')
        ));
    }

    public function testDataIsEmpty()
    {
        $model = new Vps_Model_FnF(array(
            'columns' => array('id', 'foo', 'data'),
            'data'=>array(array('id'=>1, 'foo'=>'bar', 'data'=>'')),
            'dependentModels' => array('Child'=>new Vps_Model_FieldRows(array('fieldName'=>'data')))
        ));
        $row = $model->getRow(1);
        $rows = $row->getChildRows('Child');
        $this->assertEquals(count($rows), 0);
    }

    public function testDataIsLegacy()
    {
        $model = new Vps_Model_FnF(array(
            'columns' => array('id', 'foo', 'data'),
            'data'=>array(array('id'=>1, 'foo'=>'bar', 'data'=>'vpsSerialized'.serialize(array('autoId'=>1, 'data'=>array(array('id'=>1, 'blub'=>'blub')))))),
            'dependentModels' => array('Child'=>new Vps_Model_FieldRows(array('fieldName'=>'data')))
        ));
        $row = $model->getRow(1);
        $rows = $row->getChildRows('Child');
        $this->assertEquals(count($rows), 1);
    }

    public function testCreateChildRow()
    {
        $model = new Vps_Model_FnF(array(
            'columns' => array('id', 'foo', 'data'),
            'data'=>array(array('id'=>1, 'foo'=>'bar', 'data'=>serialize(array('autoId'=>1, 'data'=>array(array('id'=>1, 'blub'=>'blub')))))),
            'dependentModels' => array('Child'=>new Vps_Model_FieldRows(array('fieldName'=>'data')))
        ));
        $row = $model->getRow(1);
        $rows = $row->getChildRows('Child');
        $this->assertEquals(count($rows), 1);
        $cRow = $row->createChildRow('Child');
        $cRow->blub = 'blub2';
        $cRow->save();
        $row->save();

        $this->assertEquals($model->getData(), array(
            array('id'=>1, 'foo'=>'bar', 'data'=>serialize(
            array(
                'data'=>array(
                    array('id'=>1, 'blub'=>'blub'),
                    array('id'=>2, 'blub'=>'blub2')
                ),
                'autoId'=>2
            )))
        ));
    }

    public function testCreateChildRowWithSavingIt()
    {
        // Einziger Unterschied zu vorher: ChildRow wird extra gespeichert, eigentlich unnötig, aber testen sollen wir das, damit die Row nicht 2x drinnen steht
        $model = new Vps_Model_FnF(array(
            'columns' => array('id', 'foo', 'data'),
            'data'=>array(array('id'=>1, 'foo'=>'bar', 'data'=>serialize(array('autoId'=>1, 'data'=>array(array('id'=>1, 'blub'=>'blub')))))),
            'dependentModels' => array('Child'=>new Vps_Model_FieldRows(array('fieldName'=>'data')))
        ));
        $row = $model->getRow(1);
        $rows = $row->getChildRows('Child');
        $this->assertEquals(count($rows), 1);
        $cRow = $row->createChildRow('Child');
        $cRow->blub = 'blub2';
        $cRow->save();
        $row->save();

        $this->assertEquals($model->getData(), array(
            array('id'=>1, 'foo'=>'bar', 'data'=>serialize(
            array(
                'data'=>array(
                    array('id'=>1, 'blub'=>'blub'),
                    array('id'=>2, 'blub'=>'blub2')
                ),
                'autoId'=>2
            )))
        ));
    }

    public function testDefaultValues()
    {
        $model = new Vps_Model_FnF(array(
            'columns' => array('id', 'foo', 'data'),
            'data'=>array(array('id'=>1, 'foo'=>'bar', 'data'=>'')),
            'dependentModels' => array('Child'=>
                new Vps_Model_FieldRows(array(
                    'fieldName'=>'data',
                    'default' => array('foo' => 'defaultFoo')
                ))
            )
        ));
        $row = $model->getRow(1);
        $cRow = $row->createChildRow('Child');
        $this->assertEquals('defaultFoo', $cRow->foo);
    }
}
