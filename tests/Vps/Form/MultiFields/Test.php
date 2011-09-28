<?php
/**
 * @group Form_MultiFields
 */
class Vps_Form_MultiFields_Test extends Vps_Test_TestCase
{
    public function testSimple()
    {
        $m1 = new Vps_Model_FnF();
        $m2 = new Vps_Model_FnF();

        $form = new Vps_Form();
        $form->setModel($m1);
        $form->add(new Vps_Form_Field_TextField('test1'));
        $form->add(new Vps_Form_Field_MultiFields($m2))
            ->setReferences(array(
                //TODO: sollte auch mit models automatisch funktionieren
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
                //TODO: sollte auch mit models automatisch funktionieren
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

        $r = Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel1')->getRow(3);
        $this->assertEquals('blab', $r->blub);
        $this->assertEquals(3, $r->id);

        $r = Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel2')->getRow(4);
        $this->assertEquals('bab', $r->foo);
        //TODO: Model Bug $this->assertEquals(3, $r->model1_id);
    }
}
