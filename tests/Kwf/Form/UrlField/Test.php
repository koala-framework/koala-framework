<?php
/**
 * @group Vps_Form_UrlField
 */
class Vps_Form_UrlField_Test extends Vps_Test_TestCase
{
    public function testUrl()
    {
        $m1 = new Vps_Model_FnF();
        $form = new Vps_Form();
        $form->setModel($m1);
        $field1 = $form->add(new Vps_Form_Field_UrlField('test'));

        $post = array(
            $field1->getFieldName() => 'http://www.örf.at'
        );
        $post = $form->processInput($form->getRow(), $post);
        $form->validate($form->getRow(), $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);

        $row = $m1->getRow($m1->select());

        $this->assertEquals('http://www.xn--rf-eka.at', $row->test);
        $loadData = $field1->load($row);
        $this->assertEquals('http://www.örf.at', $loadData['test']);
    }
}
