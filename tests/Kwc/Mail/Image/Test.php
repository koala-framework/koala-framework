<?php
/**
 * @group Kwc_Mail
 */
class Kwc_Mail_Image_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Mail_Image_Mail_Component');
        Kwf_Registry::get('config')->debug->componentCache->disable = true;
    }

    public function testImage()
    {
        $mail = $this->_root;
        $c = $mail->getComponent();

        $url = 'http://' . Kwf_Config::getValue('server.domain') . '/assets/kwf/images/rating/ratingStarFull.jpg';
        $this->assertEquals('<img src="' . $url . '" width="13" height="12" alt="" />', $c->getHtml());
    }
}