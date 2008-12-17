<?php
class Vpc_User_List_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps("Forum.User-List");
        return $ret;
    }
    protected function _getItemDirectory()
    {
        return Vps_Component_Data_Root::getInstance()->getComponentByClass(
            'Vpc_User_Directory_Component',
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
