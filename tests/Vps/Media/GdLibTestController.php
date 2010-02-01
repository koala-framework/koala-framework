<?php
class Vps_Media_GdLibTestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $im1 = imagecreatetruecolor (300, 100) or die ("Error");

        $color = '#FF0000';
        $textColorAllocated = ImageColorAllocate ($im1, hexdec(substr($color,1,2)), hexdec(substr($color,3,2)), hexdec(substr($color,5,2)));

        $backgroundColor = '#FFFF00';
        $bgColor = ImageColorAllocate ($im1, hexdec(substr($backgroundColor,1,2)), hexdec(substr($backgroundColor,3,2)), hexdec(substr($backgroundColor,5,2)));
        imageFill($im1, 0, 0, $bgColor);

        imageline($im1, 0, 0, 100, 100, $textColorAllocated);

        imagettftext($im1, 50, 0, 50, 50, $textColorAllocated, dirname(__FILE__).'/Headline/arial.ttf', 'asdf');
        header('Content-Type: image/png');
        imagepng($im1);
        imagedestroy($im1);

        exit;
    }
}
