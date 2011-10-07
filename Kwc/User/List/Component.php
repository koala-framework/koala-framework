<?php
class Kwc_User_List_Component extends Kwc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf("Forum.User-List");
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
