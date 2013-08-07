<?php
/**
 * @group MediaImage
 */
class Kwf_Media_ImageTest extends Kwf_Test_TestCase
{
    public function testImageScaleDimensions()
    {
        $this->markTestIncomplete();
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
        $dimension = array(100, 0);
        $this->_testBestFit(array(100, 30), $dimension, array(100, 30));
        $this->_testBestFit(array(101, 30), $dimension, array(100, 30));
        $this->_testBestFit(array(102, 30), $dimension, array(100, 29));//
        $this->_testBestFit(array(103, 30), $dimension, array(100, 29));
        $this->_testBestFit(array(104, 30), $dimension, array(100, 29));
        $this->_testBestFit(array(105, 30), $dimension, array(100, 29));
        $this->_testBestFit(array(106, 30), $dimension, array(100, 28));//
    }

    public function testNotZeroHeight()
    {
        //weil eine dimension von 0 gibt (logischerweise) einen imagick fehler
        $this->_testBestFit(array(100, 1), array(10, 10), array(10, 1));
    }

    public function testAvoidDivideByZero()
    {
        $dimension = array('width' => 300, 'height' => 400, 'bestfit' => true);
        $ret = Kwf_Media_Image::calculateScaleDimensions(false, $dimension);
        $this->assertEquals($ret, false);
        $ret = Kwf_Media_Image::calculateScaleDimensions(array(0, 300), $dimension);
        $this->assertEquals($ret, false);
        $ret = Kwf_Media_Image::calculateScaleDimensions(array(300, 0), $dimension);
        $this->assertEquals($ret, false);
    }

    private function _testBestFit($imageSize, $dimension, $expectedSize)
    {
        if (!isset($imageSize['width'])) {
            $imageSize['width'] = $imageSize[0];
        }
        if (!isset($imageSize['height'])) {
            $imageSize['height'] = $imageSize[1];
        }
        $dimension = array('width' => $dimension[0], 'height' => $dimension[1], 'bestfit' => true);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => $expectedSize[0],
            'height' => $expectedSize[1],
            'bestfit' => true,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => $imageSize['width'],
                'height' => $imageSize['height']
            )
       ));
        return $ret;
    }

    private function _testBestFitWithCrop($imageSize, $dimension, $expectedSize)
    {
        $dimension = array('width' => $dimension[0], 'height' => $dimension[1],
            'bestfit' => true,
            'crop' => array(
                'x' => $dimension[2],
                'y' => $dimension[3],
                'width' => $dimension[4],
                'height' => $dimension[5]
            )
        );
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => $expectedSize[0],
            'height' => $expectedSize[1],
            'bestfit' => true,
            'rotate' => null,
            'crop' => array(
                'x' => $expectedSize[2],
                'y' => $expectedSize[3],
                'width' => $expectedSize[4],
                'height' => $expectedSize[5]
            )
        ));
        return $ret;
    }

    public function testImageScale()
    {
        $this->markTestIncomplete();
        $this->_testScale(array(10, 10, false));
        $this->_testScale(array(10, 16, false));
        $this->_testScale(array(16, 10, false));

        $this->_testScale(array(10, 10, true));
        $this->_testScale(array(16, 10, true), array(10, 10));
        $this->_testScale(array(10, 16, true), array(10, 10));

        $this->_testScale(array(0, 0, true), array(16, 16));
        $this->_testScale(array(0, 0, false), array(16, 16));
    }

    private function _testScale($size, $expectedSize = null)
    {
        if (!$expectedSize) $expectedSize = $size;
        $i = Kwf_Media_Image::scale(KWF_PATH.'/images/information.png', $size);
        $im = new Imagick();
        $im->readImageBlob($i);
        $this->assertEquals($expectedSize[0], $im->getImageWidth());
        $this->assertEquals($expectedSize[1], $im->getImageHeight());
    }

    public function testScaleDimCropAspectRatio()
    {
        $dim = array('width'=>10, 'height'=>0, 'bestfit' => false, 'aspectRatio'=>3/4);
        $ret = Kwf_Media_Image::calculateScaleDimensions(array(100, 100), $dim);
        $this->assertEquals($ret, array('width'=>10, 'height'=>8,
            'bestfit' => false,
            'rotate'=>null,
            'crop' => array(
                'x' => 0,
                'y' => 10,
                'width' => 100,
                'height' => 80
            )
        ));
        $dim = array('width'=>00, 'height'=>10, 'bestfit' => false, 'aspectRatio'=>3/4);
        $ret = Kwf_Media_Image::calculateScaleDimensions(array(100, 100), $dim);
        $this->assertEquals($ret, array('width'=>8, 'height'=>10,
            'bestfit' => false,
            'rotate'=>null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 80,
                'height' => 100
            )
        ));
    }

    /**
     * Bestfit = true
     * width + height
     *  -> no-crop
     *    -> bigger
     *      -> wider: scale down, adjust width to given size
     *      -> higher: scale down, adjust higher to given size
     *      -> matches: scale down
     *    -> smaller: no changes
     *  -> crop
     *    -> bigger:
     *      -> wider: scale down, adjust width to given size
     *      -> higher: scale down, adjust height to given size
     *      -> matches: scale down
     *    -> smaller: no changes
     *
     * width/height
     *  -> no-crop
     *    -> bigger: scale down, adjust to given size
     *    -> smaller: no changes
     *  -> crop
     *    -> bigger: scale down, adjust to given size
     *    -> smaller: no changes
     */
    public function testBestFit()
    {
        //test: width + height, no-crop, bigger, wider: adjust to given size
        $ret = $this->_testBestFit(array('width' => 500, 'height' => 500), array(300, 200), array(200, 200));
        //test: width + height, no-crop, bigger, higher: adjust to given size
        $ret = $this->_testBestFit(array('width' => 500, 'height' => 500), array(200, 300), array(200, 200));
        //test: width + height, no-crop, bigger, matches: scale down
        $ret = $this->_testBestFit(array('width' => 500, 'height' => 500), array(200, 200), array(200, 200));
        //test: width + height, no-crop, smaller: no changes
        $ret = $this->_testBestFit(array('width' => 300, 'height' => 300), array(500, 500), array(300, 300));

        //test: width + height, crop, bigger, wider: scale down, adjust width to given size
        $ret = $this->_testBestFitWithCrop(array('width' => 500, 'height' => 500),
                        array(200, 200, 0, 0, 400, 200),
                        array(200, 100, 0, 0, 400, 200));
        //test: width + height, crop, bigger, higher: scale down, adjust height to given size
        $ret = $this->_testBestFitWithCrop(array('width' => 500, 'height' => 500),
                        array(200, 200, 0, 0, 200, 400),
                        array(100, 200, 0, 0, 200, 400));
        //test: width + height, crop, bigger, matches: scale down
        $ret = $this->_testBestFitWithCrop(array('width' => 500, 'height' => 500),
                        // width, height, cropX, cropY, cropWidth, cropHeight
                        array(100, 200, 0, 0, 200, 400),
                        array(100, 200, 0, 0, 200, 400));
        //test: width + height, crop, smaller: no changes
        $ret = $this->_testBestFitWithCrop(array('width' => 500, 'height' => 500),
                        array(200, 200, 0, 0, 50, 50),
                        array(50, 50, 0, 0, 50, 50));

        //test: width, no-crop, bigger: scale down, adjust to given size
        $ret = $this->_testBestFit(array('width' => 500, 'height' => 500), array(200, 0), array(200, 200));
        //test: height, no-crop, bigger: scale down, adjust to given size
        $ret = $this->_testBestFit(array('width' => 500, 'height' => 500), array(0, 200), array(200, 200));
        //test: width, no-crop, smaller: no changes
        $ret = $this->_testBestFit(array('width' => 300, 'height' => 300), array(500, 0), array(300, 300));
        //test: height, no-crop, smaller: no changes
        $ret = $this->_testBestFit(array('width' => 300, 'height' => 300), array(0, 500), array(300, 300));

        //test: width, crop, bigger: scale down, adjust to given size
        $ret = $this->_testBestFitWithCrop(array('width' => 500, 'height' => 500),
                        // width, height, cropX, cropY, cropWidth, cropHeight
                        array(100, 0, 0, 0, 200, 400),
                        array(100, 200, 0, 0, 200, 400));
        //test: height, crop, bigger: scale down, adjust to given size
        $ret = $this->_testBestFitWithCrop(array('width' => 500, 'height' => 500),
                        // width, height, cropX, cropY, cropWidth, cropHeight
                        array(0, 200, 0, 0, 200, 400),
                        array(100, 200, 0, 0, 200, 400));
        //test: width, crop, smaller: no changes
        $ret = $this->_testBestFitWithCrop(array('width' => 500, 'height' => 500),
                        // width, height, cropX, cropY, cropWidth, cropHeight
                        array(200, 0, 0, 0, 100, 100),
                        array(100, 100, 0, 0, 100, 100));
        //test: height, crop, smaller: no changes
        $ret = $this->_testBestFitWithCrop(array('width' => 500, 'height' => 500),
                        // width, height, cropX, cropY, cropWidth, cropHeight
                        array(0, 200, 0, 0, 100, 100),
                        array(100, 100, 0, 0, 100, 100));
    }

    public function testOriginal()
    {
        //test: standard parameter set and output normal, bestfit = true
        $imageSize = array('width' => 100, 'height' => 100);
        $dimension = array('width' => 0, 'height' => 0, 'bestfit' => true);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100,
            'height' => 100,
            'bestfit' => true,
            'rotate' => null
       ));
        //test: standard parameter set and output normal, bestfit = false
        $imageSize = array('width' => 100, 'height' => 100);
        $dimension = array('width' => 0, 'height' => 0, 'bestfit' => false);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100,
            'height' => 100,
            'bestfit' => true,
            'rotate' => null
       ));
        //test: every parameter set but output still normal
        $imageSize = array('width' => 100, 'height' => 100);
        $dimension = array('width' => 0, 'height' => 0, 'bestfit' => true,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 50,
                'height' => 50
            )
        );
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100,
            'height' => 100,
            'bestfit' => true,
            'rotate' => null
       ));
    }

    /**
     * Bestfit = false
     * width + height
     *  -> no-crop
     *    -> bigger
     *      -> wider: scales down, crops left and right
     *      -> higher: scales down, crops top and bottom
     *      -> matches: scales down
     *    -> smaller
     *      -> wider: scales up and crops left and right
     *      -> higher: scales up and crops top and bottom
     *      -> matches: scale up
     *  -> crop
     *    -> bigger: scales down
     *    -> smaller: scales up
     *    -> matches: no changes
     *
     * width/height
     *  -> no-crop
     *    -> bigger: adjust to given size
     *    -> smaller: scale up to given size
     *  -> crop
     *    -> bigger: adjust to given size
     *    -> smaller: scale up to given size
     */
    public function testBestFitFalse()
    {
        //test: width + height, no-crop, bigger, matches: scale down
        $imageSize = array('width' => 200, 'height' => 200);
        $dimension = array('width' => 100, 'height' => 100, 'bestfit' => false);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100,
            'height' => 100,
            'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 200,
                'height' => 200
            )
        ));
        //test: width + height, no-crop, bigger, wider: scale down, crops left and right
        $imageSize = array('width' => 200, 'height' => 100);
        $dimension = array('width' => 100, 'height' => 100, 'bestfit' => false);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100,
            'height' => 100,
            'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 50,
                'y' => 0,
                'width' => 100,
                'height' => 100
            )
        ));
        //test: width + height, no-crop, bigger, higher: scale down, crop top and bottom
        $imageSize = array('width' => 200, 'height' => 400);
        $dimension = array('width' => 100, 'height' => 100, 'bestfit' => false);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100,
            'height' => 100,
            'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 100,
                'width' => 200,
                'height' => 200
            )
        ));
        //test: width + height, no-crop, smaller, matches: scale up
        $imageSize = array('width' => 50, 'height' => 50);
        $dimension = array('width' => 100, 'height' => 100, 'bestfit' => false);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100,
            'height' => 100,
            'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 50,
                'height' => 50
            )
        ));
        //test: width + height, no-crop, smaller, wider: scale up, crop left and right
        $imageSize = array('width' => 100, 'height' => 50);
        $dimension = array('width' => 200, 'height' => 200, 'bestfit' => false);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 200,
            'height' => 200,
            'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 25,
                'y' => 0,
                'width' => 50,
                'height' => 50
            )
        ));
        //test: width + height, no-crop, smaller, higher: scale up, crop top and bottom
        $imageSize = array('width' => 50, 'height' => 100);
        $dimension = array('width' => 200, 'height' => 200, 'bestfit' => false);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 200,
            'height' => 200,
            'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 25,
                'width' => 50,
                'height' => 50
            )
        ));

        //test: width + height, crop, smaller: scales up
        $imageSize = array('width' => 200, 'height' => 200);
        $dimension = array('width' => 100, 'height' => 100, 'bestfit' => false,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 50,
                'height' => 50
            )
        );
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100, 'height' => 100, 'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 50,
                'height' => 50
            )
        ));
        //test width + height, crop, bigger: scales down
        $imageSize = array('width' => 200, 'height' => 200);
        $dimension = array('width' => 50, 'height' => 50, 'bestfit' => false,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 100,
                'height' => 100
            )
        );
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 50, 'height' => 50, 'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 100,
                'height' => 100
            )
        ));
        //test width + height, crop, matches: nothing
        $imageSize = array('width' => 200, 'height' => 200);
        $dimension = array('width' => 100, 'height' => 100, 'bestfit' => false,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 100,
                'height' => 100
            )
        );
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100, 'height' => 100, 'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 100,
                'height' => 100
            )
        ));

        //test width, no-crop, bigger: adjust to given size
        $imageSize = array('width' => 200, 'height' => 200);
        $dimension = array('width' => 100, 'height' => 0, 'bestfit' => false);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100,
            'height' => 100,
            'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 200,
                'height' => 200
            )
       ));
        //test height, no-crop, bigger: adjust to given size
        $imageSize = array('width' => 200, 'height' => 200);
        $dimension = array('width' => 0, 'height' => 100, 'bestfit' => false);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100,
            'height' => 100,
            'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 200,
                'height' =>200
            )
       ));
        //test width, no-crop, smaller: scale up to given size
        $imageSize = array('width' => 100, 'height' => 100);
        $dimension = array('width' => 200, 'height' => 0, 'bestfit' => false);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 200,
            'height' => 200,
            'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 100,
                'height' => 100
            )
       ));
        //test height, no-crop, smaller: scale up to given size
        $imageSize = array('width' => 100, 'height' => 100);
        $dimension = array('width' => 0, 'height' => 200, 'bestfit' => false);
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 200,
            'height' => 200,
            'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 100,
                'height' => 100
            )
       ));

        //test: width, crop, bigger: adjust size
        $imageSize = array('width' => 200, 'height' => 200);
        $dimension = array('width' => 50, 'height' => 0, 'bestfit' => false,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 100,
                'height' => 50
            )
        );
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 50, 'height' => 25, 'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 100,
                'height' => 50
            )
        ));
        //test: height, crop, bigger: adjust size
        $imageSize = array('width' => 200, 'height' => 200);
        $dimension = array('width' => 0, 'height' => 50, 'bestfit' => false,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 50,
                'height' => 100
            )
        );
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 25, 'height' => 50, 'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 50,
                'height' => 100
            )
        ));

        //test width, crop, smaller: scale up to given size
        $imageSize = array('width' => 200, 'height' => 200);
        $dimension = array('width' => 100, 'height' => 0, 'bestfit' => false,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 50,
                'height' => 100
            )
        );
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 100, 'height' => 200, 'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 50,
                'height' => 100
            )
        ));
        //test: height, crop, smaller: scale up to given size
        $imageSize = array('width' => 200, 'height' => 200);
        $dimension = array('width' => 0, 'height' => 100, 'bestfit' => false,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 100,
                'height' => 50
            )
        );
        $ret = Kwf_Media_Image::calculateScaleDimensions($imageSize, $dimension);
        $this->assertEquals($ret, array(
            'width' => 200, 'height' => 100, 'bestfit' => false,
            'rotate' => null,
            'crop' => array(
                'x' => 0,
                'y' => 0,
                'width' => 100,
                'height' => 50
            )
        ));
    }
}
