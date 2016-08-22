<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_Trl_Component extends Kwc_Chained_Trl_Component
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['imageUrl'] = $this->getImageUrl();
        $ret['imagePage'] = $this->getData()->getChildComponent('_imagePage');
        return $ret;
    }

    protected function _getOptions()
    {
        $ret = $this->getData()->chained->getComponent()->getOptions();
        if (Kwc_Abstract::getSetting($this->_getSetting('masterComponentClass'), 'imageTitle')) {
            $ret['title'] = nl2br($this->getRow()->title);
        }

        $masterComponentClass = Kwc_Abstract::getSetting(
            $this->_getImageEnlargeComponent()->getData()->componentClass, 'masterComponentClass'
        );
        if (Kwc_Abstract::getSetting($masterComponentClass, 'imageCaption')) {
            $ret['imageCaption'] = $this->_getImageEnlargeComponent()
                ->getRow()->image_caption;
        }
        //TODO implement fullSizeDownloadable
        return $ret;
    }

    /**
     * This function is called by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Trl_Component
     */
    public final function getOptions()
    {
        return $this->_getOptions();
    }

    /**
     * This function is called by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Trl_Component
     */
    public function getImageDimensions()
    {
        $dimension = $this->getData()->chained->getComponent()->_getSetting('dimension');
        if ($this->getData()->chained->getComponent()->getRow()->use_crop) {
            $parentDimensions = $this->_getImageEnlargeComponent()->getImageDimensions();
            $dimension['crop'] = $parentDimensions['crop'];
        }
        $data = $this->getImageData();
        return Kwf_Media_Image::calculateScaleDimensions($data['file'], $dimension);
    }

    public function getImageData()
    {
        return $this->_getImageEnlargeComponent()->getImageData();
    }

    public final function getImageDataOrEmptyImageData()
    {
        return $this->_getImageEnlargeComponent()->getImageDataOrEmptyImageData();
    }

    /**
     * This function is called by Kwc_Basic_ImageEnlarge_EnlargeTag_ImagePage_Trl_Component
     */
    public function getImageUrl()
    {
        $dimensions = $this->getImageDimensions();
        $baseUrl = $this->getBaseImageUrl();
        if ($baseUrl) {
            return str_replace('{width}', $dimensions['width'], $this->getBaseImageUrl());
        }
        return null;
    }

    public function getBaseType()
    {
        return $this->getData()->chained->getComponent()->getBaseType();
    }

    public function getBaseImageUrl()
    {
        $data = $this->getImageData();
        if ($data) {
            return Kwf_Media::getUrl($this->getData()->componentClass,
                        $this->getData()->componentId,
                        $this->getBaseType(),
                        $data['filename']);
        }
        return null;
    }

    protected function _getImageEnlargeComponent()
    {
        $d = $this->getData();
        while (!is_instance_of($d->componentClass, 'Kwc_Basic_ImageEnlarge_Trl_Component')) {
            $d = $d->parent;
        }
        return $d->getComponent();
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValidImage($id, $type, $className);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;

        $data = $component->getComponent()->getImageDataOrEmptyImageData();
        if (!$data) {
            return null;
        }
        $dimension = $component->getComponent()->getImageDimensions();

        return Kwf_Media_Output_Component::getMediaOutputForDimension($data, $dimension, $type);
    }
}
