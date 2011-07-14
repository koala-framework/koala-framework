<?php
class Vpc_Abstract_Cards_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Abstract_Cards_Model';
        $ret['default']['component'] = 'none';
        $ret['generators']['child'] = array(
            'class' => 'Vpc_Abstract_Cards_Generator',
            'component' => array(),
        );
        $ret['assetsAdmin']['dep'][] = 'VpsFormCards';
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
        $ret['componentName'] = trlVps('Choose Child');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = array();
        $ret['child'] = $this->getData()->getChildComponent(array(
            'generator' => 'child'
        ));
        return $ret;
    }

    public function hasContent()
    {
        return $this->getData()->getChildComponent(array(
            'generator' => 'child'
        ))->hasContent();
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $model = Vpc_Abstract::getSetting($componentClass, 'ownModel');
        $ret[] = new Vps_Component_Cache_Meta_Static_Model($model, '{component_id}-child');
        return $ret;
    }
}
