<?php
class Vps_View_Helper_ComponentFile
{
    public function componentFile(Vps_Component_Data $data, $file)
    {
        return Vpc_Admin::getComponentFile($data->componentClass, $file);
    }
}
