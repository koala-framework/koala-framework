<?php
/**
 * @group slow
 * @group selenium
 * @group Vpc_FormStatic
 *
 * http://vps.markus.vivid/vps/vpctest/Vpc_FormStatic_Root/form
 */
class Vpc_FormStatic_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Component_Data_Root::setComponentClass('Vpc_FormStatic_Root');
    }

    public function testForm()
    {
        $this->initTestDb(dirname(__FILE__).'/bootstrap.sql');
        $this->openVpc('/form');

        $this->type('css=#form_fullname', 'myname');
        $this->type('css=#form_content', 'lorem ipsum');
        $this->clickAndWait('css=button');
        $this->assertTextPresent('E-Mail: Please fill out the field');

        $this->type('css=#form_email', 'testmail@vivid');
        $this->clickAndWait('css=button');
        $this->assertTextPresent('is not a valid email address');

        $this->type('css=#form_email', 'test@vivid-planet.com');
        $this->clickAndWait('css=button');
        $this->assertTextPresent('The form has been submitted successfully');

        // geschickte mail checken
        $mail = $this->_getLatestMail();
        $this->assertContains('Das ist das Kontaktformular Template:', $mail->body_text);
        $this->assertContains('Email: test@vivid-planet.com', $mail->body_text);
        $this->assertContains('Fullname: myname', $mail->body_text);
        $this->assertContains('Content:'."\nlorem ipsum", $mail->body_text);

        // enquiries checken
        $enquiries = Vps_Model_Abstract::getInstance('Vps_Model_Mail');
        $row = $enquiries->getRow($enquiries->select()
            ->order('id', 'DESC')
            ->limit(1)
        );

        $this->assertEquals('', $row->sent_mail_content_html);
        $this->assertContains('Das ist das Kontaktformular Template:', $row->sent_mail_content_text);
        $this->assertContains('Email: test@vivid-planet.com', $row->sent_mail_content_text);
        $this->assertContains('Fullname: myname', $row->sent_mail_content_text);
        $this->assertContains('Content:'."\nlorem ipsum", $row->sent_mail_content_text);
    }
}
