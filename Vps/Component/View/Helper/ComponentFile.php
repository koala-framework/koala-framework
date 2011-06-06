<?php
class Vps_Component_View_Helper_ComponentFile
{
    public function componentFile(Vps_Component_Data $data, $filename)
    {
        $ext = substr($filename, strrpos($filename, '.') + 1);
        $filename = substr($filename, 0, strrpos($filename, '.'));
        return Vpc_Admin::getComponentFile($data->componentClass, $filename, $ext);
    }
}
