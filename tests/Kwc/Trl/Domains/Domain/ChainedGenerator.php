<?php
class Kwc_Trl_Domains_Domain_ChainedGenerator extends Kwc_Chained_Trl_ChainedGenerator
{
    protected function _formatSelect($parentData, $select)
    {
        $ret = parent::_formatSelect($parentData, $select);
        if (!$ret) return $ret;
        if (!$parentData && $select->hasPart(Kwf_Component_Select::WHERE_SUBROOT)) {
            $subroots = $select->getPart(Kwf_Component_Select::WHERE_SUBROOT);
            $parentData = $subroots[count($subroots)-1];
        }
        if ($parentData) {
            $ret->whereEquals('domain', $parentData->id);
            if ($parentData->componentClass != $this->_class) return null;
        }
        return $ret;
    }

    protected function _getParentDataByRow($row, $select)
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass($this->_class, array('id'=>$row->domain));
    }
}
