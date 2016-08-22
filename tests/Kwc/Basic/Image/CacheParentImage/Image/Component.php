<?php
class Kwc_Basic_Image_CacheParentImage_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['dimensions'] = array(
            'default'=>array(
                'text' => 'default',
                'width' => 10,
                'height' => 0,
                'cover' => true,
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
