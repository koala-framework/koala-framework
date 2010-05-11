<?php
class Vpc_Abstract_List_Trl_Generator extends Vpc_Chained_Trl_Generator
{
    protected function _getChainedChildComponents($parentData, Vps_Component_Select $select)
    {
        $m = Vpc_Abstract::createChildModel($this->_class);
        $ret = parent::_getChainedChildComponents($parentData, $select);
        if ($select->getPart(Vps_Component_Select::IGNORE_VISIBLE) !== true) {
            foreach ($ret as $k=>$c) {
                $r = $m->getRow($parentData->dbId.$this->getIdSeparator().$this->_getIdFromRow($c));
                if (!$r || !$r->visible) {
                    unset($ret[$k]);
                }
            }
        }
        return array_values($ret);
    }
    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $m = Vpc_Abstract::createChildModel($this->_class);
        $id = $parentData->dbId.$this->getIdSeparator().$this->_getIdFromRow($row);
        $ret['row'] = $m->getRow($id);
        if (!$ret['row']) {
            $ret['row'] = $m->createRow();
            $ret['row']->component_id = $id;
        }
        return $ret;
    }
}
