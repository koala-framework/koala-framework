<?php
class Kwc_Basic_Image_ParentImageComponent_Child_Component
    extends Kwc_Basic_ImageParent_Component
{
    public static $getMediaOutputCalled = 0;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Basic_Image_TestModel';
        $ret['dimension'] = array('width'=>16, 'height'=>16, 'cover' => true);
        return $ret;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        self::$getMediaOutputCalled++;
        return parent::getMediaOutput($id, $type, $className);
    }
}
