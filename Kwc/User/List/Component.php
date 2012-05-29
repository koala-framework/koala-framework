<?php
class Kwc_User_List_Component extends Kwc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic("Forum.User-List");
        return $ret;
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        $ret = array();
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByClass('Kwc_User_Directory_Component') as $component) {
            $ret[] = $component->componentClass;
        }
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentByClass(
            'Kwc_User_Directory_Component',
            array('subroot' => $this->getData())
        );
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $ret->order('nickname');
        return $ret;
    }
}
