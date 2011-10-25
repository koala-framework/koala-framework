<?php
/**
 * @deprecated
 */
class Kwf_View_Helper_ImageAsset extends Kwf_Component_View_Helper_Image
{
    public function imageAsset($image, $alt = '', $cssClass = null)
    {
        return $this->image($image, $alt, $cssClass);
    }
}
