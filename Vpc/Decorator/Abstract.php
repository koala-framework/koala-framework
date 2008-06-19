<?php
abstract class Vpc_Decorator_Abstract extends Vps_Component_Abstract
{
    protected $_component;

    public function __construct(Vps_Component_Abstract $component)
    {
        $this->_component = $component;
    }
    
    public function getTemplateVars()
    {
        return $this->_component->getTemplateVars();
    }

    public function getSearchVars()
    {
        return $this->_component->getSearchVars();
    }
    
    public function getStatisticVars()
    {
        return $this->_component->getStatisticVars();
    }

    public function getData()
    {
        return $this->_component->getData();
    }

}
