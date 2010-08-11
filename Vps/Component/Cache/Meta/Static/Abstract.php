<?php
abstract class Vps_Component_Cache_Meta_Static_Abstract extends Vps_Component_Cache_Meta_Abstract
{
    protected $_model;
    protected $_pattern = '{component_id}';

    public function getModelname($componentClass)
    {
        return $this->_getModelName($this->_model);
    }

    public function getPattern()
    {
        return $this->_pattern;
    }
}