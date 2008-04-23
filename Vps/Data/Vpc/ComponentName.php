<?php
class Vps_Data_Vpc_ComponentName extends Vps_Data_Abstract
{
    public function load($row)
    {
        $name = Vpc_Abstract::getSetting($row->component_class, 'componentName');
        return str_replace('.', ' -> ', $name);
    }
}
