<?php
class Kwc_Abstract_Image_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
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
        $ret['image'] = $ret['chained']; //master bild anzeigen
        if ($this->getRow()->own_image) {
            $ret['image'] = $this->getData()->getChildComponent('-image');
        }
        $imageCaptionSetting = Kwc_Abstract::getSetting($this->_getSetting('masterComponentClass'), 'imageCaption');
        if ($imageCaptionSetting) {
            $ret['image_caption'] = $this->getRow()->image_caption;
        }
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->hasContent();
        }
        return $this->getData()->chained->hasContent();
    }
}
