<?php
class Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial extends Kwf_Component_Partial_Abstract
{
    private static $_ids;
    public static $getIdsCalled;

    public static function setIds($ids)
    {
        self::$_ids = $ids;
    }

    public function getIds()
    {
        self::$getIdsCalled++;
        return self::$_ids;
    }

    public function getDefaultIds()
    {
        self::$getIdsCalled++;
        return array(1,2,3);
    }

    public static function useViewCache()
    {
        return array(
            'callback' => array('Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial', '_useViewCacheDyn'),
            'args' => array()
        );
    }

    public static function _useViewCacheDyn()
    {
        return self::$_ids == array(1,2,3);
    }
}
