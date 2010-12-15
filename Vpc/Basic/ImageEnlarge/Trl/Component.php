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

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['image'] = $this->getData()->getChildComponent('-image');
        return $ret;
    }

}
