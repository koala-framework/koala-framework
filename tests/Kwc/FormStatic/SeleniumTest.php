<?php
/**
 * @group slow
 * @group selenium
 * @group Kwc_FormStatic
 *
 * http://kwf.markus.vivid/kwf/kwctest/Kwc_FormStatic_Root/form
 */
class Kwc_FormStatic_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_FormStatic_Root');
    }

    public function testForm()
    {
        $this->initTestDb(dirname(__FILE__).'/bootstrap.sql');
        $this->openKwc('/form');

        $this->type('css=#form_fullname', 'myname');
        $this->type('css=#form_content', 'lorem ipsum');
        $this->click('css=button');
        sleep(1);
        $this->waitForConnections();
        $this->assertTextPresent('Please fill out the field');

        $this->type('css=#form_email', 'testmail@vivid');
        $this->click('css=button');
        sleep(1);
        $this->waitForConnections();
        $this->assertTextPresent('is not a valid email address');

        $this->type('css=#form_email', 'test@vivid-planet.com');
        $this->click('css=button');
        sleep(1);
        $this->waitForConnections();
        sleep(1);
        $this->assertTextPresent('The form has been submitted successfully');

        if (Kwf_Util_Model_MailLog::isAvailable()) {
            // geschickte mail checken
            $mail = $this->_getLatestMail();
            $this->assertContains('Das ist das Kontaktformular Template:', $mail->body_text);
            $this->assertContains('Email: test@vivid-planet.com', $mail->body_text);
            $this->assertContains('Fullname: myname', $mail->body_text);
            $this->assertContains('Content:'."\nlorem ipsum", $mail->body_text);
        }

        // enquiries checken
        $enquiries = Kwf_Model_Abstract::getInstance('Kwf_Model_Mail');
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
