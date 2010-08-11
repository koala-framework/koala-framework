<?php
class Vps_Component_Cache_Meta_Static_Model extends Vps_Component_Cache_Meta_Static_Abstract
{
    protected $_model;

    public function __construct($model)
    {
        $this->_model = $model;
    }

    public function getModelname($componentClass)
    {
        return $this->_getModelName($this->_model);
    }
}