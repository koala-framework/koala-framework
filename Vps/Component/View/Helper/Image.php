<?php
class Vps_Component_View_Helper_Image extends Vps_View_Helper_Image
{
    public function image($image, $alt = '', $cssClass = null)
    {
        if ($image instanceof Vpc_Abstract_Image_Component)
            $image = $image->getData();
        return parent::image($image, $alt, $cssClass);
    }

    protected function _getImageUrl($image)
    {
        if (is_string($image)) return parent::_getImageUrl($image);
        return $image->getComponent()->getImageUrl();
    }

    protected function _getImageSize($image)
    {
        if (is_string($image)) return parent::_getImageSize($image);
        return $image->getComponent()->getImageDimensions();
    }

    protected function _getImageFileContents($image)
    {
        if (is_string($image)) return parent::_getImageFileContents($image);
        return Vps_Media::getOutput($image->componentClass, $image->componentId, null);
    }

    protected function _getMailInterface()
    {
        return $this->_getRenderer();
    }
}
