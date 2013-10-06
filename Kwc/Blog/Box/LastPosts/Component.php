<?php
class Kwc_Blog_Box_LastPosts_Component extends Kwc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Kwc_Blog_Box_LastPosts_View_Component';
        return $ret;
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (is_instance_of($c, 'Kwc_Blog_Directory_Component')) {
                $ret[] = $c;
            }
        }
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentByClass('Kwc_Blog_Directory_Component', array('subroot'=>$this->getData()));
    }
}
