<?php
class Kwc_Legacy_Headline_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Headline'),
            'componentIcon' => 'text_padding_top',
            'ownModel'      => 'Kwf_Component_FieldModel',
            'rootElementClass'      => 'kwfUp-webStandard',
            'extConfig'     => 'Kwf_Component_Abstract_ExtConfig_Form'
        ));
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = array('headline1');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['headline1'] = $this->_getRow()->headline1;
        return $ret;
    }

    public function hasContent()
    {
        if (trim($this->_getRow()->headline1) != "" ) {
            return true;
        }
        return false;
    }
}
