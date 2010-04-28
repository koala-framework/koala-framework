<?php
class Vpc_Directories_Item_Directory_Trl_Generator extends Vpc_Chained_Trl_Generator
{
    protected function _getChainedChildComponents($parentData, Vps_Component_Select $select)
    {
        $limitCount = $limitOffset = null;
        if ($select->hasPart(Vps_Component_Select::LIMIT_COUNT) || $select->hasPart(Vps_Component_Select::LIMIT_OFFSET)) {
            $limitCount = $select->getPart(Vps_Component_Select::LIMIT_COUNT);
            $limitOffset = $select->getPart(Vps_Component_Select::LIMIT_OFFSET);
            $select->unsetPart(Vps_Component_Select::LIMIT_COUNT);
            $select->unsetPart(Vps_Component_Select::LIMIT_OFFSET);
        }
        $m = Vpc_Abstract::createChildModel($this->_class);
        $ret = parent::_getChainedChildComponents($parentData, $select);
        if ($m && $select->getPart(Vps_Component_Select::IGNORE_VISIBLE) !== true) {
            foreach ($ret as $k=>$c) {
                $r = $m->getRow($parentData->dbId.$this->getIdSeparator().$this->_getIdFromRow($c));
                if (!$r || !$r->visible) {
                    unset($ret[$k]);
                }
            }
        }
        $ret = array_values($ret);
        if ($limitOffset) {
            $ret = array_slice($ret, $limitOffset);
        }
        if ($limitCount) {
            $ret = array_slice($ret, 0, $limitCount);
        }
        return $ret;
    }
    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $m = Vpc_Abstract::createChildModel($this->_class);
        if ($m) {
            $id = $parentData->dbId.$this->getIdSeparator().$this->_getIdFromRow($row);
            $ret['row'] = $m->getRow($id);
            if (!$ret['row']) {
                $ret['row'] = $m->createRow();
                $ret['row']->component_id = $id;
            }
        } else {
            $ret['row'] = $ret['chained']->row;
        }

        //TODO: nicht mit settings direkt arbeiten, besser das echte generator objekt holen
        $masterCC = Vpc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $masterGen = Vpc_Abstract::getSetting($masterCC, 'generators');
        $detailGen = $masterGen['detail'];
        if (isset($ret['chained']->name)) {
            if (isset($detailGen['nameColumn'])) {
                $ret['name'] = $ret['row']->{$detailGen['nameColumn']};
            } else {
                $ret['name'] = $ret['chained']->name;
            }
        }

        if (isset($ret['chained']->filename)) {
            if (isset($detailGen['filenameColumn'])) {
                $fn = $ret['row']->{$detailGen['filenameColumn']};
            } else if (isset($ret['name'])) {
                $fn = $ret['name'];
            } else {
                $fn = '';
            }
            if (!isset($detailGen['filenameColumn']) || !$detailGen['filenameColumn']) {
                $ret['filename'] = $row->id.'_';
            }
            $ret['filename'] .= Vps_Filter::filterStatic($fn, 'Ascii');
        }
        return $ret;
    }

    public function getCacheVars($parentData)
    {
        $ret = parent::getCacheVars($parentData);
        if ($parentData) {
            foreach ($parentData->getChildComponents(array('generator'=>'detail', 'ignoreVisible'=>true)) as $c) {
                $ret[] = array(
                    'model' => $this->getModel(),
                    'id' => $c->dbId,
                    'field' => 'component_id'
                );
            }
        } else {
            //TODO
        }
        return $ret;
    }
}
