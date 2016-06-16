<?php
class Kwc_Basic_Download_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $return = parent::getTemplateVars($renderer);
        $return['infotext'] = $this->_getRow()->infotext;
        return $return;
    }
}
