<?php
class Kwf_Component_Cache_DynamicWithPartialId_TestComponent_Component extends Kwc_Abstract
    implements Kwf_Component_Partial_Interface
{
    public static $ids = array(1, 2, 3, 4, 5);

    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getPartialClass()
    {
        return 'Kwf_Component_Partial_Id';
    }

    // für helper partialPaging
    public function getPartialParams()
    {
        $ret = array();
        $ret['componentId'] = $this->getData()->componentId;
        $ret['count'] = count($this->getItemIds());
        return $ret;
    }

    public function getItemIds()
    {
        return self::$ids;
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $ret = $info;
        $ret['id'] = $nr;
        return $ret;
    }
}
