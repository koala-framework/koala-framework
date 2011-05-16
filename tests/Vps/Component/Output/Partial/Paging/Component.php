<?php
class Vps_Component_Output_Partial_Paging_Component extends Vpc_Abstract_Composite_Component
    implements Vps_Component_Partial_Interface, Vpc_Paging_ParentInterface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['paging'] = 'Vps_Component_Output_Partial_Paging_Paging';
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
        $paging = $this->getData()->getChildComponent('-paging');
        $ret = $paging->getComponent()->getPartialParams();
        $ret['count'] = $this->getPagingCount();
        return $ret;
    }

    public function getPagingCount()
    {
        return 3;
    }
}
?>