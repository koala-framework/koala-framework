<?php
/**
 * @group Vps_Form_EMailField
 */
class Vps_Form_EMailField_Test extends Vps_Test_TestCase
{
    public function testEMail()
    {
        $m1 = new Vps_Model_FnF();
        $form = new Vps_Form();
        $form->setModel($m1);
        $field1 = $form->add(new Vps_Form_Field_EMailField('test'));

        $post = array(
            $field1->getFieldName() => 'ufx@vüvüd-planet.com'
        );
        $post = $form->processInput($form->getRow(), $post);
        $form->validate($form->getRow(), $post);
        $form->prepareSave(null, $post);
        $form->save(null, $post);

        $row = $m1->getRow($m1->select());
        $this->assertEquals('ufx@xn--vvd-planet-9dbb.com', $row->test);
        $loadData = $field1->load($row);
        $this->assertEquals('ufx@vüvüd-planet.com', $loadData['test']);
    }
}
