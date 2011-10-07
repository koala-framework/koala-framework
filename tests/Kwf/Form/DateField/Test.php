<?php
/**
 * @group Kwf_Form_DateField
 */
class Kwf_Form_DateField_Test extends Kwf_Test_TestCase
{
    public function testDate()
    {
        $m1 = new Kwf_Model_FnF();
        $form = new Kwf_Form();
        $form->setModel($m1);
        $field1 = $form->add(new Kwf_Form_Field_DateField('test1'));
        $field2 = $form->add(new Kwf_Form_Field_DateField('test2'));
        $field3 = $form->add(new Kwf_Form_Field_DateField('test3'));

        $post = array(
            $field1->getFieldName() => '"2009-12-01T00:00:00"', //format von ext
            $field2->getFieldName() => trlKwf('yyyy-mm-dd'), //frontend standard wert (=null)
            $field3->getFieldName() => date(trlKwf('Y-m-d'), strtotime('2010-01-10')), //frontend wert
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
