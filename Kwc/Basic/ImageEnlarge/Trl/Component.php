<?php
class Kwc_Basic_ImageEnlarge_Trl_Component extends Kwc_Abstract_Image_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image']['component'] =
            'Kwc_Basic_ImageEnlarge_Trl_Image_Component.'.$masterComponentClass;
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $model = Kwc_Abstract::getSetting($componentClass, 'ownModel');
        $ret[] = new Kwf_Component_Cache_Meta_Static_Callback($model, '{component_id}-image');
        $ret[] = new Kwf_Component_Cache_Meta_Static_Callback($model, '{component_id}-linkTag-image');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['image'] = $this->getData()->getChildComponent('-image');
        return $ret;
    }

}
