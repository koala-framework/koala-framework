<?php
class Vps_Component_Cache_Directory_Root_List_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vps_Component_Cache_Directory_Root_List_View_Component';
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->getChildComponent('_dir');
    }
}
