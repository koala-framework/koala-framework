<?php
class Vps_Component_Cache_Meta_Static_OwnModel extends Vps_Component_Cache_Meta_Static_Abstract
{
    public function __construct($pattern = '{component_id}')
    {
        $this->_pattern = $pattern;
    }

    public function getModelname($componentClass)
    {
        if (!Vpc_Abstract::hasSetting($componentClass, 'ownModel')) return null;
        $model = Vpc_Abstract::getSetting($componentClass, 'ownModel');
        if (is_instance_of($model, 'Vpc_Basic_Text_Model')) {
            $model = new $model(array('componentClass' => $componentClass));
        }
        return $this->_getModelname($model);
    }
}