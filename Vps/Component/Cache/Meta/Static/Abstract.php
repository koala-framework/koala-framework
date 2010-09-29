<?php
abstract class Vps_Component_Cache_Meta_Static_Abstract extends Vps_Component_Cache_Meta_Abstract
{
    protected $_pattern;

    public function __construct($pattern)
    {
        $this->_pattern = $pattern;
    }

    public function getModelname($componentClass)
    {
        return null;
    }

    public function getPattern()
    {
        return $this->_pattern;
    }
}