<?php
class Kwf_Component_Output_Dynamic_Component extends Kwc_Abstract
    implements Kwf_Component_Partial_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getPartialClass()
    {
        return 'Kwf_Component_Partial_Paging';
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