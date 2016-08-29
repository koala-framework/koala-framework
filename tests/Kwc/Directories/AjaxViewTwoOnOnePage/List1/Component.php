<?php
class Kwc_Directories_AjaxViewTwoOnOnePage_List1_Component extends Kwc_Directories_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_AjaxViewTwoOnOnePage_List1_View_Component';
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_Directories_AjaxViewTwoOnOnePage_Directory_Component');
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        return array(
            'Kwc_Directories_AjaxViewTwoOnOnePage_Directory_Component'
        );
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $ret->limit(5);
        return $ret;
    }
}
