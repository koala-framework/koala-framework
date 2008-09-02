<?php
class Vpc_Directories_Top_Top extends Vpc_Directories_Top_Component
{
    protected function _getItemDirectory()
    {
        return Vps_Component_Data_Root::getInstance()
                                ->getComponentById('root_directory');
    }

}
