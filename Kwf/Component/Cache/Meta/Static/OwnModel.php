<?php
class Kwf_Component_Cache_Meta_Static_OwnModel extends Kwf_Component_Cache_Meta_Static_Abstract
{
    public function __construct($pattern = '{component_id}')
    {
        parent::__construct($pattern);
    }

    public function getModelname($componentClass)
    {
        if (!Kwc_Abstract::hasSetting($componentClass, 'ownModel')) return null;
        $model = Kwc_Abstract::getSetting($componentClass, 'ownModel');
        if (is_instance_of($model, 'Kwc_Basic_Text_Model')) {
            $model = new $model(array('componentClass' => $componentClass));
        }
        return $this->_getModelname($model);
    }
}