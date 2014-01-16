<?php
/**
 * @group MediaImage
 */
class Kwf_Media_ImageExifRotation_Test extends Kwf_Test_TestCase
{
    public function testExifRotationFallbackWrongExif()
    {
        $rotation = Kwf_Media_Image::getExifRotationFallback('../images/avatar_ghost.jpg');
        $this->assertEquals($rotation, 0);
        $rotation = Kwf_Media_Image::getExifRotationFallback('../images/colorpicker/map-red-max.png');
        $this->assertEquals($rotation, 0);
    }

    public function testExifRotationFallback()
    {
        $rotation = Kwf_Media_Image::getExifRotationFallback('Kwf/Media/ImageExifRotation/rotation/rotation0.jpg');
        $this->assertEquals($rotation, 0);
        $rotation = Kwf_Media_Image::getExifRotationFallback('Kwf/Media/ImageExifRotation/rotation/rotation1.jpg');
        $this->assertEquals($rotation, -90);
        $rotation = Kwf_Media_Image::getExifRotationFallback('Kwf/Media/ImageExifRotation/rotation/rotation2.jpg');
        $this->assertEquals($rotation, 180);
        $rotation = Kwf_Media_Image::getExifRotationFallback('Kwf/Media/ImageExifRotation/rotation/rotation3.jpg');
        $this->assertEquals($rotation, 90);
        $rotation = Kwf_Media_Image::getExifRotationFallback('Kwf/Media/ImageExifRotation/rotation/rotationM0.JPG');
        $this->assertEquals($rotation, 0);
        $rotation = Kwf_Media_Image::getExifRotationFallback('Kwf/Media/ImageExifRotation/rotation/rotationM1.JPG');
        $this->assertEquals($rotation, -90);
        $rotation = Kwf_Media_Image::getExifRotationFallback('Kwf/Media/ImageExifRotation/rotation/rotationM2.JPG');
        $this->assertEquals($rotation, 180);
        $rotation = Kwf_Media_Image::getExifRotationFallback('Kwf/Media/ImageExifRotation/rotation/rotationM3.JPG');
        $this->assertEquals($rotation, 90);
    }
}
