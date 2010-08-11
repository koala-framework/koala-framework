<?php
class Vps_Component_Cache_Meta_Static_OwnModel extends Vps_Component_Cache_Meta_Static_Abstract
{
    public function __construct($pattern = null)
    {
        if ($pattern) $this->_pattern = $pattern;
    }

    public function getModelname($componentClass)
    {
        if (!Vpc_Abstract::hasSetting($componentClass, 'ownModel')) return null;
        return Vpc_Abstract::getSetting($componentClass, 'ownModel');
    }
}