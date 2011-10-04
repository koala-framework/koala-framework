<?php
class Vpc_Basic_Headlines_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Headlines'),
            'componentIcon' => new Vps_Asset('text_padding_top'),
            'ownModel'      => 'Vps_Component_FieldModel',
            'cssClass'      => 'webStandard',
            'extConfig'     => 'Vps_Component_Abstract_ExtConfig_Form'
        ));
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
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
