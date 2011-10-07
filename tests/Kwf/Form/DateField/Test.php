<?php
/**
 * @group Vps_Form_DateField
 */
class Vps_Form_DateField_Test extends Vps_Test_TestCase
{
    public function testDate()
    {
        $m1 = new Vps_Model_FnF();
        $form = new Vps_Form();
        $form->setModel($m1);
        $field1 = $form->add(new Vps_Form_Field_DateField('test1'));
        $field2 = $form->add(new Vps_Form_Field_DateField('test2'));
        $field3 = $form->add(new Vps_Form_Field_DateField('test3'));

        $post = array(
            $field1->getFieldName() => '"2009-12-01T00:00:00"', //format von ext
            $field2->getFieldName() => trlVps('yyyy-mm-dd'), //frontend standard wert (=null)
            $field3->getFieldName() => date(trlVps('Y-m-d'), strtotime('2010-01-10')), //frontend wert
        );
        $post = $form->processInput($form->getRow(), $post);
        $form->validate($form->getRow(), $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);
        $form->afterSave(null, $post);

        $row = $m1->getRow($m1->select());
        $this->assertEquals('2009-12-01', $row->{$field1->getFieldName()});
        $this->assertEquals(null, $row->{$field2->getFieldName()});
        $this->assertEquals('2010-01-10', $row->{$field3->getFieldName()});
    }
}
