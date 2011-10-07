<?php
class Kwf_Component_View_Helper_ComponentFile
{
    public function componentFile(Kwf_Component_Data $data, $filename)
    {
        $ext = substr($filename, strrpos($filename, '.') + 1);
        $filename = substr($filename, 0, strrpos($filename, '.'));
        return Kwc_Admin::getComponentFile($data->componentClass, $filename, $ext);
    }
}
