<?php
/**
 * @group Vpc_Mail
 */
class Vpc_Mail_Image_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Mail_Image_Mail_Component');
    }

    public function testImage()
    {
        $mail = $this->_root;
        $c = $mail->getComponent();

        $url = '/assets/vps/images/rating/ratingStarFull.jpg';
        $this->assertEquals('<img src="cid:' . md5($url) . '" width="13" height="12" alt="" />', $c->getHtml(null, true));
        $this->assertEquals('<img src="' . $url . '" width="13" height="12" alt="" />', $c->getHtml());
        $images = $this->_root->getComponent()->getImages();
        $this->assertEquals(1, count($images));
        $this->assertTrue($images[0] instanceof Zend_Mime_Part);
        $this->assertEquals('ratingStarFull.jpg', $images[0]->filename);
    }
}