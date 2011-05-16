<?php
/**
 * @group Form_MultiFields
 */
class Vps_Form_MultiFields_Test extends Vps_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel1')
            ->setData(array(
                array('id'=>1, 'blub'=>'blub0'),
                array('id'=>2, 'blub'=>'blub1'),
                array('id'=>3, 'blub'=>'blub2'),
            ));
        Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel2')
            ->setData(array(
                array('id'=>1, 'model1_id'=>1, 'foo'=>'foo0', 'bar'=>'bar0', 'pos'=>1),
                array('id'=>2, 'model1_id'=>1, 'foo'=>'foo1', 'bar'=>'bar1', 'pos'=>2),
                array('id'=>3, 'model1_id'=>2, 'foo'=>'foo2', 'bar'=>'bar2', 'pos'=>1),
                array('id'=>4, 'model1_id'=>3, 'foo'=>'foo0', 'pos'=>1),
                array('id'=>5, 'model1_id'=>3, 'foo'=>'foo1', 'pos'=>2),
                array('id'=>6, 'model1_id'=>3, 'foo'=>'foo2', 'pos'=>3),
            ));
        Vps_Component_Data_Root::setComponentClass(false); //damit ModelObserver nichts macht
    }

    public function testSimple()
    {
        $m1 = new Vps_Model_FnF();
        $m2 = new Vps_Model_FnF();

        $form = new Vps_Form();
        $form->setModel($m1);
        $form->add(new Vps_Form_Field_TextField('test1'));
        $form->add(new Vps_Form_Field_MultiFields($m2))
            ->setReferences(array(
                'columns' => array('test1_id'),
                'refColumns' => array('id'),
            ))
            ->fields->add(new Vps_Form_Field_TextField('test2'));

        $post = array(
            'test1' => 'blub',
            'Vps_Model_FnF' => array(
                array('test2' => 'bab')
            )
        );
        $post = $form->processInput(null, $post);
        $form->validate(null, $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $r = $m1->getRow(1);
        $this->assertEquals('blub', $r->test1);

        $r = $m2->getRow(1);
        $this->assertEquals('bab', $r->test2);
        $this->assertEquals(1, $r->test1_id);
    }

    public function testWithComplexValidate()
    {
        $m1 = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'test1'=>'bam')
        )));
        $m2 = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'test1_id'=>1, 'test2'=>'bab')
        )));

        $form = new Vps_Form();
        $form->setModel($m1);
        $form->add(new Vps_Form_Field_TextField('test1'));
        $form->add(new Vps_Form_Field_MultiFields($m2))
            ->setReferences(array(
                'columns' => array('test1_id'),
                'refColumns' => array('id'),
            ))
            ->fields->add(new Vps_Form_Field_TextField('test2'))
                    ->addValidator(new Vps_Validate_Row_Unique());;

        $post = array(
            'test1' => 'blub',
            'Vps_Model_FnF' => array(
                array('test2' => 'bab')
            )
        );
        $post = $form->processInput(null, $post);
        $this->assertEquals(1, count($form->validate(null, $post)));
    }

    public function testWithRelations()
    {
        $form = new Vps_Form();
        $form->setModel(Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel1'));
        $form->add(new Vps_Form_Field_TextField('blub'));
        $form->add(new Vps_Form_Field_MultiFields('Model2'))
            ->fields->add(new Vps_Form_Field_TextField('foo'));

        $post = array(
            'blub' => 'blab',
            'Model2' => array(
                array('foo' => 'bab')
            )
        );
        $post = $form->processInput(null, $post);
        $form->validate(null, $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $r = Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel1')->getRow(4);
        $this->assertEquals('blab', $r->blub);
        $this->assertEquals(4, $r->id);

        $r = Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel2')->getRow(7);
        $this->assertEquals('bab', $r->foo);
        $this->assertEquals(4, $r->model1_id);
    }

    public function testWithPosInsert()
    {
        $form = new Vps_Form();
        $form->setModel(Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel1'));
        $form->add(new Vps_Form_Field_MultiFields('Model2'))
            ->fields->add(new Vps_Form_Field_TextField('foo'));

        $post = array(
            'Model2' => array(
                array('foo' => 'bab1'),
                array('foo' => 'bab2'),
                array('foo' => 'bab3')
            )
        );
        $post = $form->processInput(null, $post);
        $form->validate(null, $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $s = new Vps_Model_Select();
        $s->order('pos');
        $r = Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel1')->getRow(4);
        $data = $r->getChildRows('Model2', $s)->toArray();
        $this->assertEquals('bab1', $data[0]['foo']);
        $this->assertEquals('bab2', $data[1]['foo']);
        $this->assertEquals('bab3', $data[2]['foo']);
    }

    public function testUpdate()
    {
        $form = new Vps_Form();
        $form->setModel(Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel1'));
        $form->add(new Vps_Form_Field_MultiFields('Model2'))
            ->fields->add(new Vps_Form_Field_TextField('foo'));

        $post = array(
            'Model2' => array(
                array('foo' => 'blub0'),
                array('foo' => 'blub1'),
                array('foo' => 'blub2'),
            )
        );
        $form->setId(3);
        $post = $form->processInput(null, $post);
        $form->validate(null, $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $s = new Vps_Model_Select();
        $s->order('foo');
        $r = Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel1')->getRow(3);
        $data = $r->getChildRows('Model2', $s)->toArray();
        $this->assertEquals('blub0', $data[0]['foo']);
        $this->assertEquals('blub1', $data[1]['foo']);
        $this->assertEquals('blub2', $data[2]['foo']);
    }

    public function testWithPosUpdateAndMove()
    {
        $form = new Vps_Form();
        $form->setModel(Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel1'));
        $form->add(new Vps_Form_Field_MultiFields('Model2'))
            ->fields->add(new Vps_Form_Field_TextField('foo'));

        $post = array(
            'Model2' => array(
                array('id'=>6, 'foo' => 'blub2'), //moved to top
                array('id'=>4, 'foo' => 'blub0'),
                array('id'=>5, 'foo' => 'blub1'),
            )
        );
        $form->setId(3);
        $post = $form->processInput(null, $post);
        $form->validate(null, $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $s = new Vps_Model_Select();
        $s->order('pos');
        $r = Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel1')->getRow(3);
        $data = $r->getChildRows('Model2', $s)->toArray();
        $this->assertEquals('blub2', $data[0]['foo']);
        $this->assertEquals('blub0', $data[1]['foo']);
        $this->assertEquals('blub1', $data[2]['foo']);
    }

    public function testDeleteWithFieldRows()
    {
        $form = new Vps_Form();
        $model = new Vps_Model_FnF(array(
            'dependentModels' => array(
                'Model2' => new Vps_Model_FieldRows(array('fieldName'=>'data'))
            ),
            'data' => array(
                array('id'=>1, 'data'=>serialize(array(
                    'data' => array(
                        array('id' => 1, 'foo'=>'foo1', 'pos'=>1),
                        array('id' => 2, 'foo'=>'foo2', 'pos'=>2),
                        array('id' => 3, 'foo'=>'foo3', 'pos'=>3),
                    ),
                    'autoId' => 4
                )))
            )
        ));
        $form->setModel($model);
        $form->add(new Vps_Form_Field_MultiFields('Model2'))
            ->fields->add(new Vps_Form_Field_TextField('foo'));

        $post = array(
            'Model2' => array(
                array('id'=>1, 'foo' => 'foo1.'),
                array('id'=>3, 'foo' => 'foo3.'),
            )
        );
        $form->setId(1);
        $post = $form->processInput(null, $post);
        $form->validate(null, $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $data = $model->getData();
        $data = unserialize($data[0]['data']);
        $data = array_values($data['data']);
        $this->assertEquals(2, count($data));
        $this->assertEquals('foo1.', $data[0]['foo']);
        $this->assertEquals('foo3.', $data[1]['foo']);
    }
}
