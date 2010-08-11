<?php
class Vps_Component_Output_Partial_Random_Component extends Vpc_Abstract
    implements Vps_Component_Partial_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['partialClass'] = 'Vps_Component_Partial_Random';
        return $ret;
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
        return array('count' => 3, 'limit' => 2);
    }
}
?>