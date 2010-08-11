<?php
class Vps_Component_Cache_Meta_Static_Model extends Vps_Component_Cache_Meta_Static_Abstract
{
    public function __construct($model, $pattern = null)
    {
        $this->_model = $model;
        if ($pattern) $this->_pattern = $pattern;
    }
}