<?php
class Vpc_Abstract_Image_Trl_Image_Component extends Vpc_Abstract_Image_Component
{
    public static function getSettings($masterImageComponentClass)
    {
        $ret = parent::getSettings($masterImageComponentClass);

        //dimesion wird autom. vom master verwendet
        $ret['dimensions'] = array(
            'master'=>array(
                'text' => '',
                'width' => 0,
                'height' => 0,
                'scale' => Vps_Media_Image::SCALE_ORIGINAL
            )
        );

        return $ret;
    }

    protected function _getImageDimensions()
    {
        return $this->getData()->parent->chained->getComponent()->_getImageDimensions();
    }
}
