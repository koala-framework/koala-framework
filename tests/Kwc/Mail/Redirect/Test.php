<?php
class Kwc_Mail_Redirect_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Mail_Redirect_Mail_Component');
    }

    public function testMailWithoutRecipient()
    {
        $mail = $this->_root;
        $c = $mail->getComponent();
        $html = explode("\n", $c->getHtml());

        $this->assertEquals('<a href="http://www.vivid-planet.com">www.vivid-planet.com</a>', $html[0]);
        $this->assertEquals('<a href="http://www.vivid-planet.at">www.vivid-planet.at</a>', $html[1]);

        $text = explode("\n", $c->getText());
        $this->assertEquals('http://www.vivid-planet.com', $text[0]);
        $this->assertEquals('http://www.vivid-planet.at', $text[1]);
    }

    public function testMailWithRecipient()
    {
        $mail = $this->_root;
        $c = $mail->getComponent();
        $recipients = Kwf_Model_Abstract::getInstance('Kwc_Mail_Redirect_Mail_Recipients');
        $recipient = $recipients->getRow(
            $recipients->select()->whereEquals('email', 'ufx@vivid-planet.com')
        );

        $html = explode("\n", $c->getHtml($recipient));

        $this->assertContains('r?d=1_1_test', $html[0]);
        $this->assertContains('r?d=2_1_test', $html[1]);

        $select = new Kwf_Model_Select();
        $row = Kwf_Model_Abstract::getInstance('Kwc_Mail_Redirect_Mail_Redirect_Model')->getRow($select);
        $this->assertEquals('www.vivid-planet.com', $row->title);

        $text = explode("\n", $c->getText($recipient));
        $this->assertContains('r?d=1_1_test', $text[0]);
        $this->assertContains('r?d=2_1_test', $text[1]);
    }
}
