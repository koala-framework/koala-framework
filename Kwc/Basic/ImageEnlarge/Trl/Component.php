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

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['image'] = $this->getData()->getChildComponent('-image');
        return $ret;
    }

    public function onCacheCallback($row)
    {
        $img = $this->getData()->getChildComponent('-image');
        $cacheId = Kwf_Media::createCacheId(
            $img->componentClass, $img->componentId, 'default'
        );
        Kwf_Media::getOutputCache()->remove($cacheId);
    }
}
