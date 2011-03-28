<?php
class Vps_Component_View_Helper_Image extends Vps_View_Helper_Image
{
    public function image($image, $alt = '', $cssClass = null)
    {
        if ($image instanceof Vpc_Abstract_Image_Component)
            $image = $image->getData();
        return parent::image($image);
    }

    protected function _getImageUrl($image)
    {
        if (is_string($image)) return parent::_getImageUrl($image);

        if (!$image instanceof Vps_Component_Data ||
            !is_instance_of($image->componentClass, 'Vpc_Abstract_Image_Component')
        ) throw new Vps_Exception("No Vpc_Abstract_Image_Component Component given (is '".$image->componentClass."')");

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
        return call_user_func_array(
            array($image->componentClass, 'getMediaOutput'),
            array($c->getData()->componentId, null, $image->componentClass)
        );
    }

    protected function _getMailInterface()
    {
        return $this->_getRenderer();
    }
}
