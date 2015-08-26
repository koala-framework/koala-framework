<?php
/**
 * @group slow
 * @group selenium
 * @group Kwc_FormDynamic
 *
 * http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_FormDynamic_Basic_Root/form
 * http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_FormDynamic_Basic_Root/form2
 * http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_FormDynamic_Basic_Root/form3
 */
class Kwc_FormDynamic_Basic_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_FormDynamic_Basic_Root');
    }

    public function testTextField()
    {
        //default value
        $this->openKwc('/form');
        $this->assertElementValueEquals('css=#form_root_form-paragraphs-4', 'Def');
        $this->click('css=button');
        $this->waitForConnections();
        $this->assertElementValueEquals('css=#form_root_form-paragraphs-4', 'Def');

        //required
        $this->openKwc('/form');
        $this->assertElementPresent('css=#form_root_form-paragraphs-2');
        $this->assertElementNotPresent('css=.kwfFieldError #form_root_form-paragraphs-2');
        $this->click('css=button');
        $this->waitForConnections();
        $this->assertElementPresent('css=.kwfFieldError #form_root_form-paragraphs-2');

        //vtype email
        $this->openKwc('/form');
        $this->type('css=#form_root_form-paragraphs-3', 'foo');
        $this->click('css=button');
        $this->waitForConnections();
        $this->assertElementPresent('css=.kwfFieldError #form_root_form-paragraphs-3');
    }

    public function testCheckbox()
    {
        //default value
        $this->openKwc('/form');
        $this->assertNotChecked('css=#form_root_form-paragraphs-5');
        $this->assertChecked('css=#form_root_form-paragraphs-6');
    }

    public function testFile()
    {
        //required
        $this->openKwc('/form2');
        $this->click('css=button');
        $this->waitForConnections();
        $this->assertElementPresent('css=.kwfFieldError #form_root_form2-paragraphs-8');
    }

    public function testAdmin()
    {
        $this->openKwcEdit('Kwc_FormDynamic_Basic_Form_Paragraphs_Component', 'root_form-paragraphs');
        $this->waitForConnections();
        //test könnte natürlich verbessert werden, aber zumindest testen ob kein fehler kommt
    }

    public function testMultiCheckbox()
    {
        //required
        $this->openKwc('/form3');
        $this->click('css=button');
        $this->waitForConnections();
        $this->assertElementPresent('css=.kwfFieldError');
        $this->click('css=#form_root_form3-paragraphs-10_root_form3-paragraphs-10_1');
        $this->click('css=button');
        $this->waitForConnections();
        $this->assertTextPresent('successfully');
    }
}
