<?php
/**
 * @group Kwc_Image
 * @group Image
 */
class Kwc_Basic_Image_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_Image_Root');
        $this->_root->setFilename(null);
    }

    public function testUrl()
    {
        $c = $this->_root->getComponentById('1600');
        $url = $c->getComponent()->getImageUrl();
        $url = explode('/', trim($url, '/'));
        $this->assertEquals('Kwc_Basic_Image_FixDimensionComponent', $url[1]);
        $this->assertEquals('1600', $url[2]);
        $this->assertContains(Kwf_Media::DONT_HASH_TYPE_PREFIX.'16-', $url[3]);
        $this->assertEquals('foo.png', $url[6]);
    }

    public function testUrlWithOwnFilename()
    {
        $c = $this->_root->getComponentById('1601');
        $url = $c->getComponent()->getImageUrl();
        $url = explode('/', trim($url, '/'));
        $this->assertEquals('myname.png', $url[6]);
    }

    public function testFixDimension()
    {
        $c = $this->_root->getComponentById('1600');
        $this->assertTrue($c->hasContent());

        $this->assertEquals(array('width'=>100, 'height'=>100, 'rotate' => 0,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 16,
                'height' => 16
            )
        ),
        $c->getComponent()->getImageDimensions());
    }

    public function testGetMediaOutput()
    {
        $o = Kwc_Basic_Image_Component::getMediaOutput('1600', 'dh-16-76b7ff', 'Kwc_Basic_Image_FixDimensionComponent');
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick($o['file']);
        // File can be maximum original-size or dpr2 of dimension
        $this->assertEquals(16, $im->getImageWidth());
        $this->assertEquals(16, $im->getImageHeight());
    }

    public function testHtml()
    {
        $html = $this->_root->getComponentById(1600)->render();
        $this->assertRegExp('#^\s*<div class="kwcAbstractComposite kwcAbstractImage kwcBasicImageFixDimensionComponent[^"]*".*>'.
            '\s*<div class="container" .*>'.
            '\s*<noscript>'.
            '\s*<img src="/media/Kwc_Basic_Image_FixDimensionComponent/1600/'.Kwf_Media::DONT_HASH_TYPE_PREFIX.'16-[^/]+/[^/]+/[0-9]+/foo.png" width="100" height="100" alt="" />'.
            '\s*</noscript>'.
            '\s*</div>'.
            '\s*</div>\s*$#ms', $html);
    }

    public function testEmpty()
    {
        $c = $this->_root->getComponentById('1602');
        $this->assertFalse($c->hasContent());

        $html = $c->render();
        $this->assertRegExp('#^\s*<div class="kwcAbstractComposite kwcAbstractImage kwcBasicImageFixDimensionComponent[^"]*".*>\s*</div>\s*$#ms', $html);
    }

    public function testDimensionSetByRow()
    {
        $c = $this->_root->getComponentById('1603');

        $this->assertEquals(array('width'=>10, 'height'=>10, 'rotate' => 0,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 16,
                'height' => 16
            )
        ),
        $c->getComponent()->getImageDimensions());
    }

    public function testEmptyImagePlaceholder()
    {
        $c = $this->_root->getComponentById('1604');
        $this->assertTrue($c->hasContent());
        $url = $c->getComponent()->getImageUrl();
        $this->assertNotNull($url);
        $assertion = array('width'=>16, 'height'=>16, 'rotate'=>0,
            'crop'=> array (
                'width' => 16,
                'height' => 16,
                'x' => 0,
                'y' => 0
            ),
            'keepOriginal'=>true
        );
        $this->assertEquals($assertion, $c->getComponent()->getImageDimensions());

        $o = Kwc_Basic_Image_Component::getMediaOutput($c->componentId, Kwf_Media::DONT_HASH_TYPE_PREFIX.'16-abc', $c->componentClass);
        $this->assertNotNull($o);
        $this->assertEquals('image/png', $o['mimeType']);
        $this->assertContains('/EmptyImageComponent/empty.png', $o['file']);
    }

    public function testParentImage()
    {
        $c = $this->_root->getComponentById('1605-child');
        $this->assertEquals(Kwf_Media::DONT_HASH_TYPE_PREFIX.'{width}-551aa9', $c->getComponent()->getBaseType());

        $this->assertTrue($c->hasContent());
        $url = $c->getComponent()->getImageUrl();
        $this->assertNotNull($url);
        $url = explode('/', trim($url, '/'));
        $class = $url[1];
        $id = $url[2];
        $type = $url[3];
        $o = Kwf_Media::getOutput($class, $id, $type);
        $this->assertNotNull($o);
        $this->assertEquals('image/png', $o['mimeType']);
        $im = new Imagick();
        $im->readImage($o['file']);
        $this->assertEquals(16, $im->getImageWidth());
        $this->assertEquals(16, $im->getImageHeight());

        Kwf_Media::clearCache('Kwc_Basic_Image_ParentImageComponent_Child_Component', '1605-child', Kwf_Media::DONT_HASH_TYPE_PREFIX.'16-551aa9');

        Kwc_Basic_Image_ParentImageComponent_Child_Component::$getMediaOutputCalled = 0;
        $o = Kwf_Media::getOutput('Kwc_Basic_Image_ParentImageComponent_Child_Component', '1605-child', Kwf_Media::DONT_HASH_TYPE_PREFIX.'16-551aa9');
        $this->assertEquals(1, Kwc_Basic_Image_ParentImageComponent_Child_Component::$getMediaOutputCalled);
        $c = $this->_root->getComponentById('1605');
        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_TestModel')->getRow('1605');
        $row->kwf_upload_id = 2;
        $row->save();
        Kwf_Events_ModelObserver::getInstance()->process();
        Kwf_Media::getOutput('Kwc_Basic_Image_ParentImageComponent_Child_Component', '1605-child', Kwf_Media::DONT_HASH_TYPE_PREFIX.'16-551aa9');
        $this->assertEquals(2, Kwc_Basic_Image_ParentImageComponent_Child_Component::$getMediaOutputCalled);
    }

    public function testClearOutputCache()
    {
        $c = $this->_root->getComponentById('1600');
        $this->assertEquals(Kwf_Media::DONT_HASH_TYPE_PREFIX.'{width}-76b7ff', $c->getComponent()->getBaseType());

        Kwf_Media::clearCache('Kwc_Basic_Image_FixDimensionComponent', '1600', Kwf_Media::DONT_HASH_TYPE_PREFIX.'16-76b7ff');

        Kwc_Basic_Image_FixDimensionComponent::$getMediaOutputCalled = 0;

        Kwf_Media::getOutput('Kwc_Basic_Image_FixDimensionComponent', '1600', Kwf_Media::DONT_HASH_TYPE_PREFIX.'16-76b7ff');
        $this->assertEquals(1, Kwc_Basic_Image_FixDimensionComponent::$getMediaOutputCalled);

        Kwf_Media::getOutput('Kwc_Basic_Image_FixDimensionComponent', '1600', Kwf_Media::DONT_HASH_TYPE_PREFIX.'16-76b7ff');
        $this->assertEquals(1, Kwc_Basic_Image_FixDimensionComponent::$getMediaOutputCalled);

        Kwf_Media::clearCache('Kwc_Basic_Image_FixDimensionComponent', '1600', Kwf_Media::DONT_HASH_TYPE_PREFIX.'16-76b7ff');
        Kwf_Media::getOutput('Kwc_Basic_Image_FixDimensionComponent', '1600', Kwf_Media::DONT_HASH_TYPE_PREFIX.'16-76b7ff');
        $this->assertEquals(2, Kwc_Basic_Image_FixDimensionComponent::$getMediaOutputCalled);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Basic_Image_TestModel')->getRow('1600');
        $row->kwf_upload_id = 2;
        $row->save();
        Kwf_Events_ModelObserver::getInstance()->process();
        Kwf_Component_Data_Root::reset();

        Kwf_Media::getOutput('Kwc_Basic_Image_FixDimensionComponent', '1600', Kwf_Media::DONT_HASH_TYPE_PREFIX.'100-76b7ff');
        $this->assertEquals(3, Kwc_Basic_Image_FixDimensionComponent::$getMediaOutputCalled);
    }

    public function testBestFitWithZeroHeight()
    {
        $c = $this->_root->getComponentById('1606');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(16, $s['width']);
        $this->assertEquals(16, $s['height']);
    }

    public function testMultipleDimensions()
    {
        $c = $this->_root->getComponentById('1607');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(100, $s['width']);
        $this->assertEquals(100, $s['height']);

        $c = $this->_root->getComponentById('1608');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(200, $s['width']);
        $this->assertEquals(200, $s['height']);

        $c = $this->_root->getComponentById('1609');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(16, $s['width']);
        $this->assertEquals(16, $s['height']);

        $c = $this->_root->getComponentById('1610');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(50, $s['width']);
        $this->assertEquals(300, $s['height']);

        $c = $this->_root->getComponentById('1611');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(300, $s['width']);
        $this->assertEquals(50, $s['height']);

        $c = $this->_root->getComponentById('1612');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(50, $s['width']);
        $this->assertEquals(50, $s['height']);

        $c = $this->_root->getComponentById('1614');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(300, $s['width']); //correct?
        $this->assertEquals(300, $s['height']);
/*
        $c = $this->_root->getComponentById('1615');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(300, $s['width']); //correct?
        $this->assertEquals(300, $s['height']); //correct?

        $c = $this->_root->getComponentById('1616');
        $s = $c->getComponent()->getImageDimensions();
        $this->assertEquals(300, $s['width']); //correct?
        $this->assertEquals(300, $s['height']); //correct?
*/
    }
}
