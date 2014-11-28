<?php
/**
 * @group MediaImage
 * @group Image
 */
class Kwf_Media_ResponsiveSteps_Test extends Kwf_Test_TestCase
{
    /**
     * Max-Width-Step has to be image-width
     */
    public function testGetResponsiveWidthStepsImageSmallerThanComponentDimensions()
    {
        $imageData['file'] = '../images/stripesDark.png';
        $dim['width'] = 100;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(40);
        $this->assertEquals($steps, $resultingSteps);

        $imageData['file'] = '../images/avatar_ghost.jpg';
        $dim['width'] = 300;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(50, 150);
        $this->assertEquals($steps, $resultingSteps);

        $imageData['file'] = '../images/devices/macBook.jpg';
        $dim['width'] = 3000;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(
            40, 140, 240, 340, 440, 540, 640, 740, 840, 940, 1040, 1140, 1240,
            1340, 1440, 1540, 1640, 1740, 1840, 1940, 2040
        );
        $this->assertEquals($steps, $resultingSteps);
    }

    public function testGetResponsiveWidthStepsImageEqualComponentDimensions()
    {
        $imageData['file'] = '../images/stripesDark.png';
        $dim['width'] = 40;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(40);
        $this->assertEquals($steps, $resultingSteps);

        $imageData['file'] = '../images/avatar_ghost.jpg';
        $dim['width'] = 150;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(50, 150);
        $this->assertEquals($steps, $resultingSteps);

        $imageData['file'] = '../images/devices/macBook.jpg';
        $dim['width'] = 2040;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(
            40, 140, 240, 340, 440, 540, 640, 740, 840, 940, 1040, 1140, 1240,
            1340, 1440, 1540, 1640, 1740, 1840, 1940, 2040
        );
        $this->assertEquals($steps, $resultingSteps);
    }

    public function testGetResponsiveWidthStepsImageSmallerThanDpr2()
    {
        $imageData['file'] = '../images/stripesDark.png';
        $dim['width'] = 30;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(30, 40);
        $this->assertEquals($steps, $resultingSteps);

        $imageData['file'] = '../images/avatar_ghost.jpg';
        $dim['width'] = 100;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(100, 150);
        $this->assertEquals($steps, $resultingSteps);

        $imageData['file'] = '../images/devices/macBook.jpg';
        $dim['width'] = 1100;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(
            100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 1100, 1200,
            1300, 1400, 1500, 1600, 1700, 1800, 1900, 2000, 2040
        );
        $this->assertEquals($steps, $resultingSteps);
    }

    public function testGetResponsiveWidthStepsImageEqualDpr2()
    {
        $imageData['file'] = '../images/stripesDark.png';
        $dim['width'] = 20;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(20, 40);
        $this->assertEquals($steps, $resultingSteps);

        $imageData['file'] = '../images/avatar_ghost.jpg';
        $dim['width'] = 75;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(75, 150);
        $this->assertEquals($steps, $resultingSteps);

        $imageData['file'] = '../images/devices/macBook.jpg';
        $dim['width'] = 1020;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(
            20, 120, 220, 320, 420, 520, 620, 720, 820, 920,
            1020,
            1120, 1220, 1320, 1420, 1520, 1620, 1720, 1820, 1920, 2020,
            2040
        );
        $this->assertEquals($steps, $resultingSteps);
    }

    public function testGetResponsiveWidthStepsImageBiggerThanDpr2()
    {
        $imageData['file'] = '../images/stripesDark.png';
        $dim['width'] = 15;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(15, 30);
        $this->assertEquals($steps, $resultingSteps);

        $imageData['file'] = '../images/avatar_ghost.jpg';
        $dim['width'] = 70;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(70, 140);
        $this->assertEquals($steps, $resultingSteps);

        $imageData['file'] = '../images/devices/macBook.jpg';
        $dim['width'] = 934;
        $dim['height'] = 100;
        $steps = Kwf_Media_Image::getResponsiveWidthSteps($dim, $imageData['file']);
        $resultingSteps = array(
            34, 134, 234, 334, 434, 534, 634, 734, 834,
            934,
            1034, 1134, 1234, 1334, 1434, 1534, 1634, 1734, 1834,
            1868
        );
        $this->assertEquals($steps, $resultingSteps);
    }

    public function testGetResponsiveWidthStep()
    {
        $steps = array(
            10, 50, 100, 500
        );
        $step = Kwf_Media_Image::getResponsiveWidthStep(1, $steps);
        $this->assertEquals($step, 10);

        $step = Kwf_Media_Image::getResponsiveWidthStep(9, $steps);
        $this->assertEquals($step, 10);

        $step = Kwf_Media_Image::getResponsiveWidthStep(10, $steps);
        $this->assertEquals($step, 10);

        $step = Kwf_Media_Image::getResponsiveWidthStep(11, $steps);
        $this->assertEquals($step, 50);

        $step = Kwf_Media_Image::getResponsiveWidthStep(99, $steps);
        $this->assertEquals($step, 100);

        $step = Kwf_Media_Image::getResponsiveWidthStep(100, $steps);
        $this->assertEquals($step, 100);

        $step = Kwf_Media_Image::getResponsiveWidthStep(700, $steps);
        $this->assertEquals($step, 500);

        $step = Kwf_Media_Image::getResponsiveWidthStep(500, $steps);
        $this->assertEquals($step, 500);
    }
}
