<?php
class Kwc_Legacy_Headlines_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Headlines'),
            'componentIcon' => 'text_padding_top',
            'ownModel'      => 'Kwf_Component_FieldModel',
            'rootElementClass'      => 'kwfUp-webStandard',
            'extConfig'     => 'Kwf_Component_Abstract_ExtConfig_Form'
        ));
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = array('headline1', 'headline2');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['headline1'] = $this->_getRow()->headline1;
        $ret['headline2'] = $this->_getRow()->headline2;
        return $ret;
    }

    public function hasContent()
    {
        if (trim($this->_getRow()->headline1) != "" || trim($this->_getRow()->headline2) != "") {
            return true;
        }
        return false;
    }
}
