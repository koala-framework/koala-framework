<?php
class Kwc_Posts_Directory_Trl_Component extends Kwc_Directories_Item_Directory_Trl_Component
{

    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['write'] = $this->getData()->getChildComponent('_write');
        $ret['quickwrite'] = $this->getData()->getChildComponent('-quickwrite');
        return $ret;
    }
}
