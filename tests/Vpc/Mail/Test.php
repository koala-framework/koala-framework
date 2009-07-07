<?php
/**
 * @group Vpc_Mail
 */
class Vpc_Mail_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Mail_Mail');
    }

    public function testMail()
    {
        $mail = $this->_root;
        $c = $mail->getComponent();
        $recipients = Vps_Model_Abstract::getInstance('Vpc_Mail_Recipients');

        $this->assertEquals('htmlmail %firstname% noname', $c->getHtml());
        $this->assertEquals('textmail %firstname% noname', $c->getText());
        $this->assertEquals('Sehr geehrte%r:% %gender% %title% %lastname%', $c->getSubject());

        $recipient = $recipients->getRow(
            $recipients->select()->whereEquals('email', 'ufx@vivid-planet.com')
        );
        $this->assertEquals('htmlmail Franz Unger', $c->getHtml($recipient));
        $this->assertEquals('textmail Franz Unger', $c->getText($recipient));
        $this->assertEquals('Sehr geehrter ' . trlVps('Mr.') . ' Mag. Unger', $c->getSubject($recipient));

        $recipient = $recipients->getRow(
            $recipients->select()->whereEquals('email', 'ar@vivid-planet.com')
        );
        $this->assertEquals('htmlmail Alexandra Rainer', $c->getHtml($recipient));
        $this->assertEquals('textmail Alexandra Rainer', $c->getText($recipient));
        $this->assertEquals('Sehr geehrte ' . trlVps('Ms.') . ' Rainer', $c->getSubject($recipient));
    }
}