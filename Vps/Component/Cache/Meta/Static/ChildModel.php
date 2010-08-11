<?php
class Vps_Component_Cache_Meta_Static_ChildModel extends Vps_Component_Cache_Meta_Static_Abstract
{
    public function getModelname($componentClass)
    {
        return Vpc_Abstract::getSetting($componentClass, 'childModel');
    }
}