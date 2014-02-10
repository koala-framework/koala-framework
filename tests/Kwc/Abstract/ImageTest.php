<?php
/**
 * @group Image
 */
class Kwc_Abstract_ImageTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Abstract_Root_Component');
    }

    public function testImage404WithTooBigWidth()
    {
        $this->_assertReturns404('dh-500');
    }

    public function testImage404WhenNegativeWidth()
    {
        $this->_assertReturns404('dh--100');
    }

    public function testImage404WhenIncorrectValue()
    {
        $this->_assertReturns404('dh-100x');
        $this->_assertReturns404('default');
    }

    public function testImageDpr2SizeAvailable()
    {
        $file200 = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', 'dh-200');
        $image = new Imagick();
        $image->readimageblob($file200['contents']);
        $this->assertEquals(200, $image->getImageWidth());
    }

    private function _assertReturns404($type)
    {
        try {
            // This checks if image in defined dimensions doesn't exist
            $file = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', $type);
            $this->assertEquals(true, false);
        } catch (Kwf_Exception_NotFound $e) {
            $this->assertEquals(true, true);
        }
    }

    public function testImageWorkingType()
    {
        $file100 = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', 'dh-100');
        $image = new Imagick();
        $image->readimageblob($file100['contents']);
        $this->assertEquals(100, $image->getImageWidth());
        $file200 = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', 'dh-200');
        $image->readimageblob($file200['contents']);
        $this->assertEquals(200, $image->getImageWidth());
    }

    public function testImageCacheDeletedAfterDimensionsChange()
    {
        $html = $this->_root->getComponentById('root_imageabstract1')->render();
        // Normal size
        $file200 = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', 'dh-200');
        // Small size
        $file100 = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', 'dh-100');

        $model = Kwf_Model_Abstract::getInstance('Kwc_Abstract_Image_TestModel');
        $row = $model->getRow('root_imageabstract1');
        $row->dimension = 'default2';
        $row->kwf_upload_id = 2;
        $row->save();
        $this->_process();
        // cache for dh-100 and dh-200 must be deleted, dh-300 and dh-400 remain as zombie

        $html2 = $this->_root->getComponentById('root_imageabstract1')->render();
        $this->assertNotEquals($html, $html2);

        // Dpr2 size
        $otherfile200 = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', 'dh-200');
        // Normal size
        $otherfile100 = Kwf_Media::getOutput('Kwc_Abstract_Image_TestComponent', 'root_imageabstract1', 'dh-100');

        // SmallSize cache-id and normal-size cache-id could colide
        $this->assertNotEquals($file100, $otherfile100);
        // NormalSize cache-id and Dpr2-size cache-id could colide
        $this->assertNotEquals($file200, $otherfile200);
    }
}
