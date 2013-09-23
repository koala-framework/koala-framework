<?php
class Kwc_Basic_Image_CacheParentImage_ParentImage_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['useParentImage'] = true;
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => 'default',
                'width' => 20,
                'height' => 0,
                'bestfit' => false,
            ),
        );
        unset($ret['ownModel']);
        return $ret;
    }
}
