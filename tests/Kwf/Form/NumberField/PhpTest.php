<?php
/**
 * @group Kwf_Form_NumberField
 */
class Kwf_Form_NumberField_PhpTest extends Kwf_Test_TestCase
{
    public function testValuesFrontend()
    {
        $this->_assertPostedValue(0, 0, 'en', true);
        $this->_assertPostedValue(1, 1, 'en', true);
        $this->_assertPostedValue('1.3', 1.3, 'en', true);
        $this->_assertPostedValue(1.3, 1.3, 'en', true);

        $this->_assertPostedValue('1,3', 1.3, 'de', true);

        $this->_assertPostedValue('1.3', 5, 'de', true); //validation fails
        $this->_assertPostedValue('1,3', 5, 'en', true); //validation fails
    }

    private function _assertPostedValue($postValue, $savedValue, $language, $isFrontend)
    {
        $m1 = new Kwf_Form_NumberField_TestModel();
        $form = new Kwf_Form();
        $form->setModel($m1);
        $form->setId(1);
        $nrField = $form->add(new Kwf_Form_Field_NumberField('nr', 'Number'));

        $form->trlStaticExecute($language);

        $post = array(
            $nrField->getFieldName() => $postValue,
        );
        if ($isFrontend) {
            $post[$nrField->getFieldName().'-format'] = 'fe';
        }

        $post = $form->processInput($form->getRow(), $post);
        if (!$form->validate($form->getRow(), $post)) {
            $form->prepareSave(null, $post);
            $form->save(null, $post);
        }

        $testRow = $m1->getRow(1);
        $this->assertEquals($savedValue, $testRow->nr);
    }

    public function testValuesBackend()
    {
        $this->_assertPostedValue('1.3', 1.3, 'de', false);
        $this->_assertPostedValue('1.3', 1.3, 'en', false);
    }
}
