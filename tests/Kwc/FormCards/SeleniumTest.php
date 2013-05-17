<?php
/**
 * @group slow
 * @group selenium
 *
 * http://kwf.niko.vivid/kwf/kwctest/Kwc_FormCards_Root/form
 */
class Kwc_FormCards_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_FormCards_Root');
    }

    public function testComboValidators()
    {
        $this->openKwc('/form');

        $this->assertElementPresent('css=#root_form_form_type_cards_foo_value');

        $this->assertTrue($this->isVisible('css=#root_form_form_type_cards_foo_value'));
        $this->assertFalse($this->isVisible('css=#root_form_form_type_cards_bar_value'));

        $this->type('css=#root_form_form_fullname', 'myname');
        $this->click('css=button');
        sleep(1);
        $this->waitForConnections();
        $this->assertElementPresent('css=.form_type_cards_foo_value.kwfFieldError');

        $this->type('css=#root_form_form_type_cards_foo_value', 'foo');
        $this->click('css=button');
        sleep(1);
        $this->waitForConnections();
        sleep(1);
        $this->assertTextPresent('The form has been submitted successfully');
    }
    
    public function testComboChangeSelect()
    {
        $this->openKwc('/form');

        $this->assertTrue($this->isVisible('css=#root_form_form_type_cards_foo_value'));
        $this->assertFalse($this->isVisible('css=#root_form_form_type_cards_bar_value'));
        
        $this->select('css=#root_form_form_type_cards_type', 'Bar');

        $this->assertFalse($this->isVisible('css=#root_form_form_type_cards_foo_value'));
        $this->assertTrue($this->isVisible('css=#root_form_form_type_cards_bar_value'));
    }

    public function testRadioValidators()
    {
        $this->openKwc('/formradio');

        $this->assertElementPresent('css=#root_formradio_form_type_cards_foo_value');

        $this->assertTrue($this->isVisible('css=#root_formradio_form_type_cards_foo_value'));
        $this->assertFalse($this->isVisible('css=#root_formradio_form_type_cards_bar_value'));

        $this->type('css=#root_formradio_form_fullname', 'myname');
        $this->click('css=button');
        sleep(1);
        $this->waitForConnections();
        $this->assertElementPresent('css=.form_type_cards_foo_value.kwfFieldError');

        $this->type('css=#root_formradio_form_type_cards_foo_value', 'foo');
        $this->click('css=button');
        sleep(1);
        $this->waitForConnections();
        sleep(1);
        $this->assertTextPresent('The form has been submitted successfully');
    }
    
    public function testRadioChangeSelect()
    {
        $this->openKwc('/formradio');

        $this->assertTrue($this->isVisible('css=#root_formradio_form_type_cards_foo_value'));
        $this->assertFalse($this->isVisible('css=#root_formradio_form_type_cards_bar_value'));
        
        $this->click('css=#root_formradio_type2');

        $this->assertFalse($this->isVisible('css=#root_formradio_form_type_cards_foo_value'));
        $this->assertTrue($this->isVisible('css=#root_formradio_form_type_cards_bar_value'));
    }
}
