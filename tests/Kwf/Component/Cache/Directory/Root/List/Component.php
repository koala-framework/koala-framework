<?php
class Kwf_Component_Cache_Directory_Root_List_Component extends Kwc_Directories_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['view'] = 'Kwf_Component_Cache_Directory_Root_List_View_Component';
        return $ret;
    }

    public static function getItemDirectoryClasses($componentClass)
    {
        return array('Kwf_Component_Cache_Directory_Root_Directory_Component');
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->getChildComponent('_dir');
    }
}
