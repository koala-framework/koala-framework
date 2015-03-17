<?php
class Kwc_Posts_Directory_Trl_Component extends Kwc_Directories_Item_Directory_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['write'] = $this->getData()->getChildComponent('_write');
        $ret['quickwrite'] = $this->getData()->getChildComponent('-quickwrite');
        return $ret;
    }
}
