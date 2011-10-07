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
        $m = $this->getModel();
        $ret = parent::_getChainedChildComponents($parentData, $select);
        if ($m && $select->getPart(Vps_Component_Select::IGNORE_VISIBLE) !== true && $parentData) {
            //kann nur gemacht werden nur wenn parentData vorhanden
            $ids = array();
            foreach ($ret as $k=>$c) {
                $ids[] = $parentData->dbId.$this->getIdSeparator().$this->_getIdFromRow($c);
            }
            foreach ($this->_getRows($ids) as $r) {
                if ($r) $visible[$r->component_id] = $r->visible;
            }
            foreach ($ret as $k=>$c) {
                $id = $parentData->dbId.$this->getIdSeparator().$this->_getIdFromRow($c);
                if (!isset($visible[$id]) || !$visible[$id]) {
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

    public function getModel()
    {
        return Vpc_Abstract::createChildModel($this->_class);
    }

    public function getChildIds($parentData, $select = array())
    {
        $ret = parent::getChildIds($parentData, $select);
        $m = $this->getModel();
        if ($m && $select->getPart(Vps_Component_Select::IGNORE_VISIBLE) !== true && $parentData) {
            $ids = array();
            $prefix = $parentData->dbId . $this->getIdSeparator();
            foreach ($ret as $id) {
                $ids[] = $prefix . $id;
            }
            $select = $m->select()
                ->whereEquals('visible', true)
                ->whereEquals('component_id', $ids);
            $ret = array();
            $len = strlen($prefix);
            foreach ($m->getIds($select) as $id) {
                $ret[] = substr($id, $len);
            }
        }
        return $ret;
    }

    protected function _createData($parentData, $row, $select)
    {
        //visible überprüfung wird _getChainedChildComponents auch schon gemacht
        //aber nur wenn parentData dort schon verfügbar ist
        //für fälle wo es das nicht war hier unten nochmal überprüfen
        $m = $this->getModel();
        if ($m && $select->getPart(Vps_Component_Select::IGNORE_VISIBLE) !== true) {
            $r = $this->_getRow($parentData->dbId.$this->getIdSeparator().$this->_getIdFromRow($row));
            if (!$r || !$r->visible) {
                return null;
            }
        }
        return parent::_createData($parentData, $row, $select);
    }

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $m = $this->getModel();
        if ($m) {
            $id = $parentData->dbId.$this->getIdSeparator().$this->_getIdFromRow($row);
            $ret['row'] = $this->_getRow($id);
            if (!$ret['row']) {
                $ret['row'] = $m->createRow();
                $ret['row']->component_id = $id;
            }
            /* should not be necessary but I don't know if it's needed somewhere
        } else {
            $ret['row'] = $ret['chained']->row;
            */
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
}
