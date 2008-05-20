<?php
class Vps_Form_Field_ComponentContainer extends Vps_Form_Field_Abstract
{
    private $_componentId;

    public function __construct($componentId)
    {
        $this->_componentId = $componentId;
        $this->_init();
    }
    public function getTemplateVars($values)
    {
        return array('component' => $this->_componentId);
    }
}
