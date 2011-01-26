<?php
/**
 * @group Vps_Form_DateTimeField
 */
class Vps_Form_DateTimeField_Test extends Vps_Test_TestCase
{
    public function testDateTime()
    {
        $m1 = new Vps_Model_FnF();
        $form = new Vps_Form();
        $form->setModel($m1);
        $field1 = $form->add(new Vps_Form_Field_DateTimeField('dtftest1', 'dtf label'))
            ->setAllowBlank(false);
        $field2 = $form->add(new Vps_Form_Field_DateTimeField('dtftest2', 'dtf label2'))
            ->setAllowBlank(false);
        $field3 = $form->add(new Vps_Form_Field_DateTimeField('dtftest3', 'dtf label2'))
            ->setAllowBlank(false);

        $post = array(
            $field1->getFieldName() => '2009-12-15T13:43:00',
            $field2->getFieldName() => '"2009-12-01T13:43:59"',
            $field3->getFieldName() => '"209-12-1'
        );
        $post = $form->processInput($form->getRow(), $post);
        $form->validate($form->getRow(), $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);

        $row = $m1->getRow($m1->select());

        $this->assertEquals('2009-12-15 13:43:00', $row->{$field1->getFieldName()});
        $this->assertEquals('2009-12-01 13:43:59', $row->{$field2->getFieldName()});
        $this->assertNull($row->{$field3->getFieldName()});
    }
}
