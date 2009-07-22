<?php
/**
 * @group Vpc_Mail
 */
class Vpc_Mail_Placeholder_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Mail_Placeholder_Mail');
    }

    public function testMail()
    {
        $mail = $this->_root;
        $c = $mail->getComponent();
        $recipients = Vps_Model_Abstract::getInstance('Vpc_Mail_Placeholder_Recipients');

        $this->assertEquals('htmlmail %firstname% noname', $c->getHtml());
        $this->assertEquals('textmail %firstname% noname', $c->getText());
        $this->assertEquals('%sehr_geehrt%', $c->getSubject());

        $recipient = $recipients->getRow(
            $recipients->select()->whereEquals('email', 'ufx@vivid-planet.com')
        );
        $this->assertEquals('htmlmail Franz Unger', $c->getHtml($recipient));
        $this->assertEquals('textmail Franz Unger', $c->getText($recipient));
        $this->assertEquals('Sehr geehrter Herr Mag. Unger', $c->getSubject($recipient));

        $recipient = $recipients->getRow(
            $recipients->select()->whereEquals('email', 'ar@vivid-planet.com')
        );
        $this->assertEquals('htmlmail Alexandra Rainer', $c->getHtml($recipient));
        $this->assertEquals('textmail Alexandra Rainer', $c->getText($recipient));
        $this->assertEquals('Sehr geehrte Frau Rainer', $c->getSubject($recipient));
    }
}