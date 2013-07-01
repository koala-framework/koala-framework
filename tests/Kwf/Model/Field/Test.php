<?php
/**
 * @group Model
 * @group Model_Field
 */
class Kwf_Model_Field_Test extends Kwf_Test_TestCase
{
    public function testFnFField()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'foo', 'data'),
            'data'=>array(array('id'=>1, 'foo'=>'bar', 'data'=>json_encode(array('blub'=>'blub')))),
            'siblingModels' => array(new Kwf_Model_Field(array('fieldName'=>'data')))
        ));

        $row = $model->getRow(1);
        $this->assertEquals($row->foo, 'bar');
        $this->assertEquals($row->blub, 'blub');
        $row->blub1 = 'blub1';
        $row->save();

        $this->assertEquals(array(array('id'=>1, 'foo'=>'bar', 'data'=>json_encode(
                                  array('blub'=>'blub', 'blub1'=>'blub1')))), $model->getData());
        $row = $model->getRow(1);
        $this->assertEquals($row->blub1, 'blub1');
        $this->assertNull($row->notExistent);

        $row = $model->createRow();
        $row->id = 2;
        $row->foo = 'newFoo';
        $row->blub = 'newBlub';
        $row->save();
        $this->assertEquals($model->getData(), array(
            array('id'=>1, 'foo'=>'bar', 'data'=>json_encode(array('blub'=>'blub', 'blub1'=>'blub1'))),
            array('id'=>2, 'foo'=>'newFoo', 'data'=>json_encode(array('blub'=>'newBlub'))),
        ));
    }

    public function testFnFFieldField()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'foo', 'data'),
            'data'=>array(array('id'=>1, 'foo'=>'bar', 'data'=>json_encode(array('blub'=>'blub',
                        'data'=>json_encode(array('blub1'=>'blub1')))))),
            'siblingModels' => array(new Kwf_Model_Field(array(
                'fieldName'=>'data',
                'columns' => array('blub', 'data'),
                'siblingModels' => array(new Kwf_Model_Field(array(
                    'fieldName' => 'data',
                    'columns' => array('blub1', 'blub2')
                )))
            )))
        ));

        $this->assertTrue($model->hasColumn('foo'));
        $this->assertTrue($model->hasColumn('blub'));
        $this->assertTrue($model->hasColumn('blub1'));
        $this->assertTrue($model->hasColumn('blub2'));

        $row = $model->getRow(1);
        $this->assertEquals($row->foo, 'bar');
        $this->assertEquals($row->blub, 'blub');
        $this->assertEquals($row->blub1, 'blub1');
        $row->blub2 = 'blub2';
        $row->save();

        $this->assertEquals($model->getData(), array(array('id'=>1, 'foo'=>'bar', 'data'=>json_encode(array('blub'=>'blub',
                'data'=>json_encode(array('blub1'=>'blub1', 'blub2'=>'blub2')))))));
        $row = $model->getRow(1);
        $this->assertEquals($row->blub2, 'blub2');
    }

    public function testDataIsEmpty()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'foo', 'data'),
            'data'=>array(array('id'=>1, 'foo'=>'bar', 'data'=>json_encode(''))),
            'siblingModels' => array(new Kwf_Model_Field(array('fieldName'=>'data')))
        ));
        $row = $model->getRow(1);
        $row->blub = 1;
        $row->save();
        $this->assertEquals($model->getData(), array(
            array('id'=>1, 'foo'=>'bar', 'data'=>json_encode(array('blub'=>1)))
        ));
    }

    public function testDataIsLegacy()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'foo', 'data'),
            'data'=>array(array('id'=>1, 'foo'=>'bar', 'data'=>'kwfSerialized'.json_encode(array('blub'=>'blub')))),
            'siblingModels' => array(new Kwf_Model_Field(array('fieldName'=>'data')))
        ));
        $row = $model->getRow(1);
        $row->blub = 1;
        $row->save();
        $this->assertEquals($model->getData(), array(
            array('id'=>1, 'foo'=>'bar', 'data'=>json_encode(array('blub'=>1)))
        ));
    }

    public function testDefaultValues()
    {
        $model = new Kwf_Model_FnF(array(
            'default' => array('foo1'=>'defaultFoo1'),
            'columns' => array('id', 'foo1', 'data'),
            'siblingModels' => array(new Kwf_Model_Field(array(
                'fieldName'=>'data',
                'default' => array('foo2'=>'defaultFoo2'),
            )))
        ));
        $row = $model->createRow();
        $this->assertEquals('defaultFoo1', $row->foo1);
        $this->assertEquals('defaultFoo2', $row->foo2);
    }

    public function testWithProxy()
    {
        $fnf = new Kwf_Model_FnF(array(
            'columns' => array('id', 'foo1', 'data')
        ));
        $model = new Kwf_Model_Proxy(array(
            'proxyModel' => $fnf,
            'siblingModels' => array(new Kwf_Model_Field(array(
                'fieldName'=>'data'
            )))
        ));
        $row = $model->createRow();
        $row->foo1 = 'bar';
        $row->blub = 'bum';
        $row->save();
        $this->assertEquals($fnf->getData(), array(
            array('id'=>1, 'foo1'=>'bar', 'data'=>json_encode(array('blub'=>'bum')))
        ));
    }

    public function testDuplicate()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'foo1', 'data'),
            'siblingModels' => array(new Kwf_Model_Field(array(
                'fieldName'=>'data',
            )))
        ));
        $row = $model->createRow();
        $row->foo1 = 'foo1';
        $row->blub = 'blub';
        $row->blub2 = 'blub2';
        $row->save();

        $row = $row->duplicate(array('blub2' => 'xxx'));
        $this->assertEquals($row->foo1, 'foo1');
        $this->assertEquals($row->blub, 'blub');
        $this->assertEquals($row->blub2, 'xxx');
    }

    public function testFieldEvents()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'foo', 'data'),
            'siblingModels' => array(new Kwf_Model_Field_FieldModel(array(
                'fieldName'=>'data',
            )))
        ));
        $row = $model->createRow();
        $row->foo = 'foo';
        $row->blub = 'blub';
        $row->blub2 = 'blub2';
        $row->save();

        $counts = Kwf_Model_Field_FieldModelRow::$counts;
        $this->assertEquals(1, $counts['beforeInsert']);
        $this->assertEquals(1, $counts['afterInsert']);
        $this->assertEquals(0, $counts['beforeUpdate']);
        $this->assertEquals(0, $counts['afterUpdate']);
        $this->assertEquals(1, $counts['beforeSave']);
        $this->assertEquals(1, $counts['afterSave']);
        $this->assertEquals(0, $counts['beforeDelete']);
        $this->assertEquals(0, $counts['afterDelete']);

        $row->save();

        $counts = Kwf_Model_Field_FieldModelRow::$counts;
        $this->assertEquals(1, $counts['beforeInsert']);
        $this->assertEquals(1, $counts['afterInsert']);
        $this->assertEquals(1, $counts['beforeUpdate']);
        $this->assertEquals(1, $counts['afterUpdate']);
        $this->assertEquals(2, $counts['beforeSave']);
        $this->assertEquals(2, $counts['afterSave']);
        $this->assertEquals(0, $counts['beforeDelete']);
        $this->assertEquals(0, $counts['afterDelete']);

        // delete nicht gecheckt, data row kann selbst nicht gelöscht werden,
        // die wird sowieso von der hauptrow mitgerissen
    }
}
