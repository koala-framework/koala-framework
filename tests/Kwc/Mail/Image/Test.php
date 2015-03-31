<?php
/**
 * @group Kwc_Mail
 * @group Image
 */
class Kwc_Mail_Image_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Mail_Image_Mail_Component');
    }

    public function testImage()
    {
        $mail = $this->_root;
        $c = $mail->getComponent();
        $html = $c->getHtml();

        $mail = new Kwf_Mail();
        $mail->setBodyHtml($html, null, Zend_Mime::ENCODING_8BIT);
        $url = 'http://' . Kwf_Config::getValue('server.domain') . '/assets/kwf/images/rating/ratingStarFull.jpg';
        $this->assertEquals('<img src="' . $url . '" width="13" height="12" alt="" />', $mail->getBodyHtml(true));

        $mail = new Kwf_Mail();
        $mail->setAttachImages(true);
        $mail->setBodyHtml($html, null, Zend_Mime::ENCODING_8BIT);
        $this->assertEquals('<img src="cid:d8c33cb4a497ce1919c3662b7c5df05f" width="13" height="12" alt="" />', $mail->getBodyHtml(true));
    }
}
