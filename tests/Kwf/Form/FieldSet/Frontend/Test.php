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

    //since the ajax submit this test doesn't make too much sense
    public function testRemembersValueOnSubmit()
    {
        $this->openKwc('/form');
        $this->assertFalse($this->isChecked("css=fieldset legend input"));
        $this->click("css=fieldset legend input");
        $this->click("css=button.submit");
        $this->waitForConnections();
        $this->assertTrue($this->isChecked("css=fieldset legend input"));
    }

    public function testNoValidationInFieldset()
    {
        $this->openKwc('/form');
        $this->type("css=#foo1", 'blah');
        $this->click("css=fieldset legend input");
        $this->click("css=button.submit");
        $this->waitForConnections();
        $this->assertTextPresent(trlKwf('Please fill out the field'));
        $this->assertTrue($this->isChecked("css=fieldset legend input"));
        $this->click("css=fieldset legend input");
        $this->click("css=button.submit");
        $this->waitForConnections();
        $this->assertTextNotPresent(trlKwf('Please fill out the field'));
    }
}
