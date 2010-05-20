<?php
/**
 * @deprecated
 */
class Vps_View_Helper_ImageAsset extends Vps_View_Helper_Image
{
    public function imageAsset($image, $alt = '', $cssClass = null)
    {
        return $this->image($image, $alt, $cssClass);
    }
}
