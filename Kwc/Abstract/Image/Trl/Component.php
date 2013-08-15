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

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['image'] = $this->getData();
        $imageCaptionSetting = Kwc_Abstract::getSetting($this->_getSetting('masterComponentClass'), 'imageCaption');
        if ($imageCaptionSetting) {
            $ret['image_caption'] = $this->getRow()->image_caption;
        }
        return $ret;
    }

    public function getImageUrl()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getImageUrl();
        } else {
            $data = $this->getData()->chained->getComponent()->getImageDataOrEmptyImageData();
            if ($data && $data['filename']) {
                $id = $this->getData()->componentId;
                $type = $this->getData()->chained->getComponent()->getImageUrlType();
                return Kwf_Media::getUrl($this->getData()->componentClass, $id, $type, $data['filename']);
            }
        }
        return null;
    }

    public function getImageDimensions()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getImageDimensions();
        } else {
            return $this->getData()->chained->getComponent()->getImageDimensions();
        }
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
        return Kwf_Media_Output_Component::isValid($id);
    }

    //if own_image getMediaOutput of image child component is used
    public static function getMediaOutput($id, $type, $className)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id);
        return call_user_func(
            array(get_class($c->chained->getComponent()), 'getMediaOutput'),
            $c->chained->componentId, $type, $c->chained->componentClass
        );
    }
}
