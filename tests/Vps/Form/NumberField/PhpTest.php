<?php
/**
 * @group Vps_Form_NumberField
 */
class Vps_Form_NumberField_PhpTest extends Vps_Test_TestCase
{
    public function testNoSettings()
    {
        $m1 = new Vps_Form_NumberField_TestModel();
        $form = new Vps_Form();
        $form->setModel($m1);
        $form->setId(1);
        $nrField = $form->add(new Vps_Form_Field_NumberField('nr', 'Number'));

        $post = array(
            $nrField->getFieldName() => 1
        );
        $post = $form->processInput($form->getRow(), $post);
        $form->validate($form->getRow(), $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $testRow = $m1->getRow(1);
        $this->assertEquals(1, $testRow->nr);
    }

    public function testValue0()
    {
        $m1 = new Vps_Form_NumberField_TestModel();
        $form = new Vps_Form();
        $form->setModel($m1);
        $form->setId(1);
        $nrField = $form->add(new Vps_Form_Field_NumberField('nr', 'Number'));

        $post = array(
            $nrField->getFieldName() => 0
        );
        $post = $form->processInput($form->getRow(), $post);
        $form->validate($form->getRow(), $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);
        $testRow = $m1->getRow(1);
        $this->assertEquals(0, $testRow->nr);
    }
}
