<?php
class Kwf_Component_Output_Partial_Paging_Component extends Kwc_Abstract_Composite_Component
    implements Kwf_Component_Partial_Interface, Kwc_Paging_ParentInterface
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['paging'] = 'Kwf_Component_Output_Partial_Paging_Paging';
        $ret['contentWidth'] = 600;
        return $ret;
    }

    public static function getPartialClass($componentClass)
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