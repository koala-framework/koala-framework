<?php
class Vps_Component_Cache_Meta_Static_OwnModel extends Vps_Component_Cache_Meta_Static_Abstract
{
    public function getModelname($componentClass)
    {
        if (!Vpc_Abstract::hasSetting($componentClass, 'ownModel')) return null;
        return Vpc_Abstract::getSetting($componentClass, 'ownModel');
    }
}