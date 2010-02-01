<?php
class Vps_Media_GdLibTestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $size = 300;
        $image=imagecreatetruecolor($size, $size);

        // something to get a white background with black border
        $back = imagecolorallocate($image, 255, 255, 255);
        $border = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, $size - 1, $size - 1, $back);
        imagerectangle($image, 0, 0, $size - 1, $size - 1, $border);

        $yellow_x = 100;
        $yellow_y = 75;
        $red_x    = 120;
        $red_y    = 165;
        $blue_x   = 187;
        $blue_y   = 125;
        $radius   = 150;

        // allocate colors with alpha values
        $yellow = imagecolorallocatealpha($image, 255, 255, 0, 75);
        $red    = imagecolorallocatealpha($image, 255, 0, 0, 75);
        $blue   = imagecolorallocatealpha($image, 0, 0, 255, 75);

        // drawing 3 overlapped circle
        imagefilledellipse($image, $yellow_x, $yellow_y, $radius, $radius, $yellow);
        imagefilledellipse($image, $red_x, $red_y, $radius, $radius, $red);
        imagefilledellipse($image, $blue_x, $blue_y, $radius, $radius, $blue);

        // don't forget to output a correct header!
        header('Content-type: image/png');

        // and finally, output the result
        imagepng($image);
        imagedestroy($image);

        exit;

        $im1 = imagecreatetruecolor (300, 100) or die ("Error");
        imagealphablending($im1, true);
        imageSaveAlpha($im1, true);

        $color = '#FF0000';
        $textColorAllocated = ImageColorAllocate ($im1, hexdec(substr($color,1,2)), hexdec(substr($color,3,2)), hexdec(substr($color,5,2)));

        $backgroundColor = '#FFFF00';
//         $bgColor = ImageColorAllocate ($im1, hexdec(substr($backgroundColor,1,2)), hexdec(substr($backgroundColor,3,2)), hexdec(substr($backgroundColor,5,2)));
        $bgColor = imagecolorallocatealpha($im1, 255, 255, 255, 126);
        imageFill($im1, 0, 0, $bgColor);

        imageline($im1, 0, 0, 100, 100, $textColorAllocated);

        imagettftext($im1, 50, 0, 50, 50, $textColorAllocated, dirname(__FILE__).'/Headline/arial.ttf', 'asdf');
        header('Content-Type: image/png');
        imagepng($im1);
        imagedestroy($im1);

        exit;
    }
}
