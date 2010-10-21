<?php
/**
 * @group MediaImage
 */
class Vps_Media_ImageTest extends PHPUnit_Framework_TestCase
{
    public function testImageScaleDimensions()
    {
        $this->_testBestFit(array(16, 16), array(10, 50), array(10, 10));
        $this->_testBestFit(array(16, 16), array(50, 10), array(10, 10));

        $dimension = array(100, 100);
        $this->_testBestFit(array(100, 100), $dimension, array(100, 100));
        $this->_testBestFit(array(200, 200), $dimension, array(100, 100));
        $this->_testBestFit(array(80, 80), $dimension, array(80, 80));
        $this->_testBestFit(array(95, 60), $dimension, array(95, 60));
        $this->_testBestFit(array(60, 95), $dimension, array(60, 95));
        $this->_testBestFit(array(200, 100), $dimension, array(100, 50));
        $this->_testBestFit(array(100, 200), $dimension, array(50, 100));
        $this->_testBestFit(array(100, 80), $dimension, array(100, 80));
        $this->_testBestFit(array(80, 100), $dimension, array(80, 100));
        $dimension = array(100, 50);
        $this->_testBestFit(array(100, 100), $dimension, array(50, 50));
        $this->_testBestFit(array(200, 200), $dimension, array(50, 50));
        $this->_testBestFit(array(40, 40), $dimension, array(40, 40));
        $this->_testBestFit(array(45, 30), $dimension, array(45, 30));
        $this->_testBestFit(array(30, 45), $dimension, array(30, 45));
        $this->_testBestFit(array(200, 100), $dimension, array(100, 50));
        $this->_testBestFit(array(100, 200), $dimension, array(25, 50));
        $this->_testBestFit(array(100, 30), $dimension, array(100, 30));

    }

    public function testNotZeroHeight()
    {
        //weil eine dimension von 0 gibt (logischerweise) einen imagick fehler
        $this->_testBestFit(array(100, 1), array(10, 10), array(10, 1));
    }

    public function testAvoidDivideByZero()
    {
        $dimension = array('width' => 300, 'height' => 400, 'scale' => Vps_Media_Image::SCALE_BESTFIT);
        $ret = Vps_Media_Image::calculateScaleDimensions(false, $dimension);
        $this->assertEquals($ret, false);
        $ret = Vps_Media_Image::calculateScaleDimensions(array(0, 300), $dimension);
        $this->assertEquals($ret, false);
        $ret = Vps_Media_Image::calculateScaleDimensions(array(300, 0), $dimension);
        $this->assertEquals($ret, false);
    }

    private function _testBestFit($imageSize, $dimension, $expectedSize)
    {
        $dimension = array('width' => $dimension[0], 'height' => $dimension[1], 'scale' => Vps_Media_Image::SCALE_BESTFIT);
        $ret = Vps_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array('width' => $expectedSize[0], 'height' => $expectedSize[1], 'scale' => Vps_Media_Image::SCALE_BESTFIT));
    }

    public function testImageScaleDeform()
    {
        $this->_testScale(array(10, 10, Vps_Media_Image::SCALE_DEFORM));
        $this->_testScale(array(16, 10, Vps_Media_Image::SCALE_DEFORM));
        $this->_testScale(array(10, 16, Vps_Media_Image::SCALE_DEFORM));

        $this->_testScale(array(10, 10, Vps_Media_Image::SCALE_CROP));
        $this->_testScale(array(10, 16, Vps_Media_Image::SCALE_CROP));
        $this->_testScale(array(16, 10, Vps_Media_Image::SCALE_CROP));

        $this->_testScale(array(10, 10, Vps_Media_Image::SCALE_BESTFIT));
        $this->_testScale(array(16, 10, Vps_Media_Image::SCALE_BESTFIT), array(10, 10));
        $this->_testScale(array(10, 16, Vps_Media_Image::SCALE_BESTFIT), array(10, 10));

        $this->_testScale(array(16, 16, Vps_Media_Image::SCALE_ORIGINAL));
        $this->_testScale(array(10, 10, Vps_Media_Image::SCALE_ORIGINAL), array(16, 16));
    }

    private function _testScale($size, $expectedSize = null)
    {
        if (!$expectedSize) $expectedSize = $size;
        $i = Vps_Media_Image::scale(VPS_PATH.'/images/information.png', $size);
        $im = new Imagick();
        $im->readImageBlob($i);
        $this->assertEquals($expectedSize[0], $im->getImageWidth());
        $this->assertEquals($expectedSize[1], $im->getImageHeight());
    }
}
