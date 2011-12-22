<?php
class Kwc_Basic_Image_CacheParentImage_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['dimensions'] = array(
            'default'=>array(
                'text' => 'default',
                'width' => 10,
                'height' => 0,
                'scale' => Kwf_Media_Image::SCALE_DEFORM
            ),
        );

        $ret['ownModel'] = 'Kwc_Basic_Image_CacheParentImage_Image_TestModel';

        $ret['generators']['parentImage'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Basic_Image_CacheParentImage_ParentImage_Component'
        );

        return $ret;
    }
}
