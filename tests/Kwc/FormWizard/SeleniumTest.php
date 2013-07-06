<?php
/**
 * @group slow
 * @group selenium
 * @group Kwc_FormWizard
 *
 * http://kwf.benjamin.vivid/kwf/kwctest/Kwc_FormWizard_Root/form?
 */
class Kwc_FormWizard_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_FormWizard_Root');
    }

    public function testWizardPost()
    {
        //default value
        $this->openKwc('/form');
        $this->assertElementPresent('id=root_form-form1_form_text');
        $this->clickAndWait('css=button');
        $this->assertElementPresent('id=root_form-form2_form_number');
        $this->clickAndWait('css=button');
        $this->assertText('css=.webStandard.webSuccess.kwcFormSuccess', 'The form has been submitted successfully.');
    }

    public function testWizardAjax()
    {
        $this->openKwc('/form2');
        $this->assertElementPresent('id=root_form2-form1_form_text');
        $this->click('css=.kwcFormWizardWizardFormAjaxForm1 button');
        $this->waitForConnections();
        $this->assertElementPresent('id=root_form2-form2_form_number');
        $this->click('css=.kwcFormWizardWizardFormAjaxForm2 button');
        $this->waitForConnections();
        $this->assertText('css=.webStandard.webSuccess.kwcFormSuccess', 'The form has been submitted successfully.');
    }
}