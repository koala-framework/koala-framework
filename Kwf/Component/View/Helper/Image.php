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
            if (!$image instanceof Kwf_Component_Data ||
                !is_instance_of($image->componentClass, 'Kwc_Abstract_Image_Component')
            ) throw new Kwf_Exception("No Kwc_Abstract_Image_Component Component given (is '".$image->componentClass."')");

            $url = $image->getComponent()->getImageUrl();
        }
        
        if ($this->_getRenderer() instanceof Kwf_View_MailInterface &&
            substr($url, 0, 1) == '/'
        ) {
            $domain = Kwf_Config::getValue('server.domain');
            if (!is_string($image)) {
                $data = $image;
                while ($data && !Kwc_Abstract::getFlag($data->componentClass, 'hasDomain')) {
                    $data = $data->parent;
                }
                if ($data) {
                    $domain = $data->getComponent()->getDomain();
                }
            }
            $url = "http://$domain$url";
        }

        return $url;
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
            array($image->getComponent()->getData()->componentId, null, $image->componentClass)
        );
    }
}
