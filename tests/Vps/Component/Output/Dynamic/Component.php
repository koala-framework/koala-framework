<?php
class Vps_Component_Output_Dynamic_Component extends Vpc_Abstract
    implements Vps_Component_Partial_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getPartialClass()
    {
        return 'Vps_Component_Partial_Paging';
    }

    public function getPartialVars($partial, $nr, $info)
    {
        return array('item' => 'bar' . $nr);
    }

    public function getPartialCacheVars($nr)
    {
        return array();
    }

    public function getPartialParams()
    {
        return array('count' => 3);
    }
}
?>