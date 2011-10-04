<?php
class Vpc_Basic_Image_ParentImageComponent_Child_Component
    extends Vpc_Basic_Image_Component
{
    public static $getMediaOutputCalled = 0;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Image_TestModel';
        $ret['dimensions'] = array(array('width'=>16, 'height'=>16, 'scale'=>Vps_Media_Image::SCALE_DEFORM));
        $ret['useParentImage'] = true;
        return $ret;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        self::$getMediaOutputCalled++;
        return parent::getMediaOutput($id, $type, $className);
    }
}
