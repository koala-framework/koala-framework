<?php
class Kwc_Abstract_Image_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Abstract_Image_Trl_Image_Component.'.$masterComponentClass
        );
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['throwContentChangedOnOwnMasterModelUpdate'] = true;
        $ret['throwHasContentChangedOnMasterRowColumnsUpdate'] = array('kwf_upload_id');
        return $ret;
    }

    public function getBaseType()
    {
        return $this->getData()->chained->getComponent()->getBaseType();
    }

    public function getBaseImageUrl()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getBaseImageUrl();
        } else {
            $data = $this->getData()->chained->getComponent()->getImageDataOrEmptyImageData();
            if ($data && $data['filename']) {
                return Kwf_Media::getUrl($this->getData()->componentClass,
                    $this->getData()->componentId,
                    $this->getBaseType(),
                    $data['filename']);
            }
        }
        return null;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['image'] = $this->getData();
        $imageCaptionSetting = Kwc_Abstract::getSetting($this->_getSetting('masterComponentClass'), 'imageCaption');
        if ($imageCaptionSetting) {
            $ret['image_caption'] = $this->getRow()->image_caption;
        }
        $ret['baseUrl'] = $this->getBaseImageUrl();
        if ($this->getRow()->own_image) {
            $imageData = $this->getImageData();
            if ($imageData) {
                $steps = Kwf_Media_Image::getResponsiveWidthSteps($this->getImageDimensions(), $imageData['file']);
                $ret['minWidth'] = $steps[0];
                $ret['maxWidth'] = end($steps);
            }
        }
        return $ret;
    }

    public function getImageData()
    {
        return $this->_getCorrectImageComponent()->getImageData();
    }

    public final function getImageDataOrEmptyImageData()
    {
        return $this->_getCorrectImageComponent()->getImageDataOrEmptyImageData();
    }

    public function getImageUrl()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getImageUrl();
        } else {
            $baseUrl = $this->getBaseImageUrl();
            if ($baseUrl) {
                $dimensions = $this->getImageDimensions();
                return str_replace('{width}', $dimensions['width'], $baseUrl);
            }
        }
        return null;
    }

    private function _getCorrectImageComponent()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent();
        } else {
            return $this->getData()->chained->getComponent();
        }
    }

    public function getImageDimensions()
    {
        return $this->_getCorrectImageComponent()->getImageDimensions();
    }

    public function hasContent()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->hasContent();
        }
        return $this->getData()->chained->hasContent();
    }

    public function getExportData()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getExportData();
        }
        $ret = parent::getExportData();
        $ret['imageUrl'] = $this->getImageUrl();
        return $ret;
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValidImage($id, $type);
    }

    //if own_image getMediaOutput of image child component is used
    public static function getMediaOutput($id, $type, $className)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        return call_user_func(
            array(get_class($c->chained->getComponent()), 'getMediaOutput'),
            $c->chained->componentId, $type, $c->chained->componentClass
        );
    }
}
