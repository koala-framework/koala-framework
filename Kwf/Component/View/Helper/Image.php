<?php
class Kwf_Component_View_Helper_Image extends Kwf_View_Helper_Image
{
    public function image($image, $alt = '', $cssClass = null)
    {
        if ($image instanceof Kwc_Abstract_Image_Component)
            $image = $image->getData();
        return parent::image($image, $alt, $cssClass);
    }

    protected function _getImageUrl($image)
    {
        if (is_string($image)) {
            $url = parent::_getImageUrl($image);
        } else {
            $url = $image->getComponent()->getImageUrl();
        }
        return $url;
    }

    protected function _getImageSize($image)
    {
        if (is_string($image)) return parent::_getImageSize($image);
        return $image->getComponent()->getImageDimensions();
    }
}
