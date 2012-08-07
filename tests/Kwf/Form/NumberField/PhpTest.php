<?php
/**
 * @group Kwf_Form_NumberField
 */
class Kwf_Form_NumberField_PhpTest extends Kwf_Test_TestCase
{
    public function testValues()
    {
        $this->_assertPostedValue(0, 0, 'en');
        $this->_assertPostedValue(1, 1, 'en');
        $this->_assertPostedValue('1.3', 1.3, 'en');
        $this->_assertPostedValue(1.3, 1.3, 'en');

        $this->_assertPostedValue('1,3', 1.3, 'de');

        $this->_assertPostedValue('1.3', 5, 'de'); //validation fails
        $this->_assertPostedValue('1,3', 5, 'en'); //validation fails
    }

    private function _assertPostedValue($postValue, $savedValue, $language)
    {
        $m1 = new Kwf_Form_NumberField_TestModel();
        $form = new Kwf_Form();
        $form->setModel($m1);
        $form->setId(1);
        $nrField = $form->add(new Kwf_Form_Field_NumberField('nr', 'Number'));

        $form->trlStaticExecute($language);

        $post = array(
            $nrField->getFieldName() => $postValue
        );
        $post = $form->processInput($form->getRow(), $post);
        if (!$form->validate($form->getRow(), $post)) {
            $form->prepareSave(null, $post);
            $form->save(null, $post);
        }

        $testRow = $m1->getRow(1);
        $this->assertEquals($savedValue, $testRow->nr);
    }
}
