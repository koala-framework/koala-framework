<?php
class Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_Component extends Kwc_Abstract
    implements Kwf_Component_Partial_Interface
{
    public static $getPartialVarsCalled;
    public static function getPartialClass($componentClass)
    {
        return 'Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial';
    }

    public function getPartialVars($partial, $id, $info)
    {
        self::$getPartialVarsCalled++;
        return array('id'=>$id);
    }
}
