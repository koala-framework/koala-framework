<?php
/**
 * @group slow
 * @group selenium
 * @group Vpc_FormDynamic
 *
 * http://vps.vps.niko.vivid/vps/vpctest/Vpc_FormDynamic_Basic_Root/form
 * http://vps.vps.niko.vivid/vps/vpctest/Vpc_FormDynamic_Basic_Root/form2
 * http://vps.vps.niko.vivid/vps/vpctest/Vpc_FormDynamic_Basic_Root/form3
 */
class Vpc_FormDynamic_Basic_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass('Vpc_FormDynamic_Basic_Root');
    }

    public function testTextField()
    {
        //default value
        $this->openVpc('/form');
        $this->assertElementValueEquals('css=#form_root_form-paragraphs-4', 'Def');
        $this->clickAndWait('css=button');
        $this->assertElementValueEquals('css=#form_root_form-paragraphs-4', 'Def');

        //required
        $this->openVpc('/form');
        $this->assertElementPresent('css=#form_root_form-paragraphs-2');
        $this->assertElementNotPresent('css=.vpsFieldError #form_root_form-paragraphs-2');
        $this->clickAndWait('css=button');
        $this->assertTextPresent('Required: Please fill out');
        $this->assertElementPresent('css=.vpsFieldError #form_root_form-paragraphs-2');

        //vtype email
        $this->openVpc('/form');
        $this->type('css=#form_root_form-paragraphs-3', 'foo');
        $this->clickAndWait('css=button');
        $this->assertTextPresent('EMail: \'foo\' is not a valid');
        $this->assertElementPresent('css=.vpsFieldError #form_root_form-paragraphs-3');
    }

    public function testCheckbox()
    {
        //default value
        $this->openVpc('/form');
        $this->assertNotChecked('css=#form_root_form-paragraphs-5');
        $this->assertChecked('css=#form_root_form-paragraphs-6');
    }

    public function testFile()
    {
        //required
        $this->openVpc('/form2');
        $this->clickAndWait('css=button');
        $this->assertTextPresent('Required: Please fill out');
        $this->assertElementPresent('css=.vpsFieldError #form_root_form2-paragraphs-8');
    }

    public function testAdmin()
    {
        $this->openVpcEdit('Vpc_FormDynamic_Basic_Form_Paragraphs_Component', 'root_form-paragraphs');
        $this->waitForConnections();
        //test könnte natürlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }

    public function testMultiCheckbox()
    {
        //required
        $this->openVpc('/form3');
        $this->clickAndWait('css=button');
        $this->assertTextPresent('Required: Please fill out');
        $this->assertElementPresent('css=.vpsFieldError');
        $this->click('css=#form_root_form3-paragraphs-10_root_form3-paragraphs-10_1');
        $this->clickAndWait('css=button');
        $this->assertTextPresent('successfully');
    }
}