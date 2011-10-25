<?php
class Kwc_Directories_Top_Top extends Kwc_Directories_Top_Component
{
    public static function getItemDirectoryClasses($componentClass)
    {
        return array('Kwc_Directories_Top_Directory');
    }

    protected function _getItemDirectory()
    {
        return Kwf_Component_Data_Root::getInstance()
                                ->getComponentById('root_directory');
    }
}
