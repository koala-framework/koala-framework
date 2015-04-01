<?php
/**
 * @group Kwf_Form_NumberField
 */
class Kwf_Form_NumberField_PhpTest extends Kwf_Test_TestCase
{
    public function setUp()
    {
        $trlElements = array();
        $trlElements['kwf']['de']['.-decimal separator'] = ',';
        $trlElements['kwf']['de']['C-locale'] = 'de_AT.UTF-8, de.UTF-8, de_DE.UTF-8';
        Kwf_Trl::getInstance()->setTrlElements($trlElements);
    }

    public function testValuesFrontendEn()
    {
        $this->_assertPostedValue(0, 0, 'en', '0');
        $this->_assertPostedValue(1, 1, 'en', '1');
        $this->_assertPostedValue('1.3', 1.3, 'en', '1.3');
        $this->_assertPostedValue(1.3, 1.3, 'en', '1.3');
    }

    public function testValuesFrontendDe()
    {
        $this->_assertPostedValue(0, 0, 'de', '0');
        $this->_assertPostedValue(1, 1, 'de', '1');
        $this->_assertPostedValue('1,3', 1.3, 'de', '1,3');
    }

    public function testValuesFrontendDeInvalid()
    {
        $this->_assertPostedValue('1.3', 5, 'de', '1.3'); //validation fails
    }

    public function testValuesFrontendEnInvalid()
    {
        $this->_assertPostedValue('1,3', 5, 'en', '1,3'); //validation fails
    }

    public function testValuesBackend()
    {
        $this->_assertPostedValue('1.3', 1.3, 'de', false);
        $this->_assertPostedValue('1.3', 1.3, 'en', false);
    }

    public function testValuesAllowDecimals()
    {
        $this->_assertPostedValue('3', 3, 'de', false, false);
    }

    private function _assertPostedValue($postValue, $savedValue, $language, $frontendValue, $allowDecimals = null)
    {
        $m1 = new Kwf_Form_NumberField_TestModel();
        $form = new Kwf_Form();
        $form->setModel($m1);
        $form->setId(1);
        $nrField = $form->add(new Kwf_Form_Field_NumberField('nr', 'Number'));
        $nrField->setAllowDecimals($allowDecimals);

        $form->trlStaticExecute($language);

        $post = array(
            $nrField->getFieldName() => $postValue,
        );
        if ($frontendValue !== false) {
            $post[$nrField->getFieldName().'-format'] = 'fe';
        }

        $post = $form->processInput(null, $post);
        if (!$form->validate(null, $post)) {
            $form->prepareSave(null, $post);
            $form->save(null, $post);
        }

        $testRow = $m1->getRow(1);
        $this->assertEquals($savedValue, $testRow->nr);
        $values = $form->load(null, $post);
        $this->assertEquals($values['nr'], $postValue);

        if ($frontendValue !== false) {
            $html = $nrField->getTemplateVars($values);
            $this->assertTrue(!!preg_match('#name="nr" value="(.*?)"#', $html['html'], $m));
            $this->assertEquals($frontendValue, $m[1]);
        }
    }

}
