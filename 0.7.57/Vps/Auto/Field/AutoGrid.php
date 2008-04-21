<?php
class Vps_Auto_Field_AutoGrid extends Vps_Auto_Field_Abstract
{
    public function __construct($controllerUrl = null)
    {
        $this->setControllerUrl($controllerUrl);
        $this->setXtype('autogrid');
    }
}
