<?php
/**
 * @group slow
 * @group seleniuim
 */
class Kwc_FormCache_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_FormCache_Root');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testFormCache()
    {
        // check if form-data still there after submit-failure
        $this->openKwc('/form');
        $this->type('css=#root_form_form_fullname', 'myname');
        $this->type('css=#root_form_form_content', 'lorem ipsum');
        $this->click('css=button');
        sleep(1);
        $this->assertElementPresent('css=.form_email.kwfFieldError');
        $this->assertElementValueEquals('id=root_form_form_fullname', 'myname');
        $this->assertElementValueEquals('id=root_form_form_content', 'lorem ipsum');


        // check if form empty after reload (failure-values not cached)
        $this->openKwc('/form');
        $this->assertElementValueEquals('id=root_form_form_fullname', '');
        $this->assertElementValueEquals('id=root_form_form_content', '');


        // check if values changed from first submit-failure (no cached failure-values returned)
        $this->type('css=#root_form_form_fullname', 'myname1');
        $this->type('css=#root_form_form_content', 'lorem ipsum1');
        $this->click('css=button');
        sleep(1);
        $this->assertElementPresent('css=.form_email.kwfFieldError');
        $this->assertElementValueEquals('id=root_form_form_fullname', 'myname1');
        $this->assertElementValueEquals('id=root_form_form_content', 'lorem ipsum1');

        // check ob successfully
        $this->openKwc('/form');
        $this->type('css=#root_form_form_fullname', 'myname');
        $this->type('css=#root_form_form_content', 'lorem ipsum');
        $this->type('css=#root_form_form_email', 'test@vivid-planet.com');
        $this->click('css=button');
        sleep(1);
        $this->assertTextPresent('The form has been submitted successfully');


        // check if form empty after successfully submit
        $this->openKwc('/form');
        $this->assertElementValueEquals('id=root_form_form_fullname', '');
        $this->assertElementValueEquals('id=root_form_form_content', '');
        $this->assertElementValueEquals('id=root_form_form_email', '');

        //TODO maybe needed to check if cached values are saved
    }
}
