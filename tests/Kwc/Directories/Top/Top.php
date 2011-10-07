<?php
class Kwc_Directories_Top_Top extends Kwc_Directories_Top_Component
{
    protected function _getItemDirectory()
    {
        return Kwf_Component_Data_Root::getInstance()
                                ->getComponentById('root_directory');
    }

}
