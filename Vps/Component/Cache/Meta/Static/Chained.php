<?php
class Vps_Component_Cache_Meta_Static_Chained extends Vps_Component_Cache_Meta_Abstract
{
    private $_sourceComponentClass;

    public function __construct($sourceComponentClass)
    {
        $this->_sourceComponentClass = $sourceComponentClass;
    }

    public function getSourceComponentClass()
    {
        return $this->_sourceComponentClass;
    }
}