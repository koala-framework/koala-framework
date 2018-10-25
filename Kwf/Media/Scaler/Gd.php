<?php
class Kwf_Media_Scaler_Gd extends Kwf_Media_Scaler_Abstract
{
    public function scale($source, $size, $mimeType, array $options)
    {
        $srcSize = getimagesize($source);
        if ($srcSize[2] == 1) {
            $source = imagecreatefromgif($source);
        } elseif ($srcSize[2] == 2) {
            $source = imagecreatefromjpeg($source);
        } elseif ($srcSize[2] == 3) {
            $source = imagecreatefrompng($source);
        }
        if (isset($size['rotate']) && $size['rotate']) {
            $source = imagerotate($source, $size['rotate'], 0);
        }
        $destination = imagecreatetruecolor($size['width'], $size['height']);
        imagefill($destination, 0, 0, imagecolorallocate($destination, 255, 255, 255));
        imagecopyresampled($destination, $source, 0, 0, $size['crop']['x'], $size['crop']['y'],
            $size['width'], $size['height'],
            $size['crop']['width'], $size['crop']['height']);
        ob_start();
        if ($srcSize[2] == 1) {
            imagegif($destination);
        } elseif ($srcSize[2] == 2) {
            imagejpeg($destination);
        } elseif ($srcSize[2] == 3) {
            imagepng($destination);
        }
        $ret = ob_get_contents();
        ob_end_clean();
        return $ret;
    }
}
