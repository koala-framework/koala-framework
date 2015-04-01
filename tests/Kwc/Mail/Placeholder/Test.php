<?php
/**
 * @group Kwc_Mail
 */
class Kwc_Mail_Placeholder_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Mail_Placeholder_Mail_Component');
    }

    public function testMail()
    {
        $mail = $this->_root;
        $c = $mail->getComponent();
        $recipients = Kwf_Model_Abstract::getInstance('Kwc_Mail_Placeholder_Mail_Recipients');

        $this->assertEquals('htmlmail %firstname% noname', $c->getHtml());
        $this->assertEquals('textmail %firstname% noname', $c->getText());
        $this->assertEquals('%salutation_polite%', $c->getSubject());

        $recipient = $recipients->getRow(
            $recipients->select()->whereEquals('email', 'ufx@vivid-planet.com')
        );
        $this->assertEquals('htmlmail Franz Unger', $c->getHtml($recipient));
        $this->assertEquals('textmail Franz Unger', $c->getText($recipient));
        $this->assertEquals(trlKwf('Dear Mr. {0} {1}', array('Mag.', 'Unger')), $c->getSubject($recipient));

        $recipient = $recipients->getRow(
            $recipients->select()->whereEquals('email', 'ar@vivid-planet.com')
        );
        $this->assertEquals('htmlmail Alexandra Rainer', $c->getHtml($recipient));
        $this->assertEquals('textmail Alexandra Rainer', $c->getText($recipient));
        $this->assertEquals(trlKwf('Dear Mrs. {0}', array('Rainer')), $c->getSubject($recipient));
    }
}
