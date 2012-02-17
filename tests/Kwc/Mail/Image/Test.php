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

        $url = '/assets/kwf/images/rating/ratingStarFull.jpg';
        $this->assertEquals('<img src="' . $url . '" width="13" height="12" alt="" />', $c->getHtml());
        $images = $this->_root->getComponent()->getImages();
        $this->assertEquals(1, count($images));
        $this->assertTrue($images[0] instanceof Zend_Mime_Part);
        $this->assertEquals('ratingStarFull.jpg', $images[0]->filename);
    }
}