<?php
/**
 * @group selenium
 * @group slow
 * @group Vps_Form_FieldSet
 */
class Vps_Form_FieldSet_Frontend_Test extends Vps_Test_SeleniumTestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Form_FieldSet_Frontend_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
        parent::setUp();
    }

    public function testRemembersValueOnSubmit()
    {
        $this->openVpc('/form');
        $this->assertFalse($this->isChecked("css=fieldset legend input"));
        $this->click("css=fieldset legend input");
        $this->clickAndWait("css=button.submit");
        $this->assertTrue($this->isChecked("css=fieldset legend input"));
    }

    public function testNoValidationInFieldset()
    {
        $this->openVpc('/form');
        $this->type("css=#foo1", 'blah');
        $this->click("css=fieldset legend input");
        $this->clickAndWait("css=button.submit");
        $this->assertTextPresent(trlVps('An error has occurred'));
        $this->assertTrue($this->isChecked("css=fieldset legend input"));
        $this->click("css=fieldset legend input");
        $this->clickAndWait("css=button.submit");
        $this->assertTextNotPresent(trlVps('An error has occurred'));
    }
}
