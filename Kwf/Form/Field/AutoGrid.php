<?php
class Vps_Form_Field_AutoGrid extends Vps_Form_Field_Abstract
{
    public function __construct($controllerUrl = null)
    {
        $this->setControllerUrl($controllerUrl);
        $this->setXtype('autogrid');
    }
}
