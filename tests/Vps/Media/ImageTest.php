<?php
class Vps_Media_ImageTest extends PHPUnit_Framework_TestCase
{
    public function testImageScale()
    {
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
    
    private function _testBestFit($image, $dimension, $target)
    {
        $dimension = array('width' => $dimension[0], 'height' => $dimension[1], 'scale' => Vps_Media_Image::SCALE_BESTFIT);
        $ret = Vps_Media_Image::calculateScaleDimensions($image, $dimension);
        $this->assertEquals($ret, array('width' => $target[0], 'height' => $target[1], 'scale' => Vps_Media_Image::SCALE_BESTFIT));
    }
}
