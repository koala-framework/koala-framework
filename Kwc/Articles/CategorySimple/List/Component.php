<?php
class Kwc_Articles_CategorySimple_List_Component
    extends Kwc_Directories_CategorySimple_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Articles');
        $ret['categoryComponentClass'] = 'Kwc_Articles_CategorySimple_Component';
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_Articles_Directory_Component', array('limit' => 1));
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        return array('Kwc_Articles_Directory_Component');
    }
}
