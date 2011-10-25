<?php
/**
 * @group selenium
 * @group slow
 * @group Kwf_Form_FieldSet
 */
class Kwf_Form_FieldSet_Frontend_Test extends Kwf_Test_SeleniumTestCase
{
    private $_root;

    public function setUp()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwf_Form_FieldSet_Frontend_Root');
        $this->_root = Kwf_Component_Data_Root::getInstance();
        parent::setUp();
    }

    public function testRemembersValueOnSubmit()
    {
        $this->openKwc('/form');
        $this->assertFalse($this->isChecked("css=fieldset legend input"));
        $this->click("css=fieldset legend input");
        $this->clickAndWait("css=button.submit");
        $this->assertTrue($this->isChecked("css=fieldset legend input"));
    }

    public function testNoValidationInFieldset()
    {
        $this->openKwc('/form');
        $this->type("css=#foo1", 'blah');
        $this->click("css=fieldset legend input");
        $this->clickAndWait("css=button.submit");
        $this->assertTextPresent(trlKwf('An error has occurred'));
        $this->assertTrue($this->isChecked("css=fieldset legend input"));
        $this->click("css=fieldset legend input");
        $this->clickAndWait("css=button.submit");
        $this->assertTextNotPresent(trlKwf('An error has occurred'));
    }
}
