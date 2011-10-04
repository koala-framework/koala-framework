<?php
class Vpc_Basic_ImageEnlarge_Trl_Component extends Vpc_Abstract_Image_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image']['component'] =
            'Vpc_Basic_ImageEnlarge_Trl_Image_Component.'.$masterComponentClass;
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $model = Vpc_Abstract::getSetting($componentClass, 'ownModel');
        $ret[] = new Vps_Component_Cache_Meta_Static_Callback($model, '{component_id}-image');
        $ret[] = new Vps_Component_Cache_Meta_Static_Callback($model, '{component_id}-linkTag-image');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['image'] = $this->getData()->getChildComponent('-image');
        return $ret;
    }

}
