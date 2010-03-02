<?php
class Vpc_Chained_Trl_Generator extends Vps_Component_Generator_Abstract
{
    protected function _init()
    {
        parent::_init();
        $this->_inherits = $this->_getChainedGenerator()->getInherits();
    }

    public function getPagesControllerConfig($component)
    {
        $ret = $this->_getChainedGenerator()->getPagesControllerConfig($component, $this->getClass());
        $ret['allowDrag'] = false;
        $ret['allowDrop'] = false;
        return $ret;
    }

    protected function _getChainedData($data)
    {
        if (isset($data->chained)) return $data->chained;

        if (is_instance_of($this->_class, 'Vpc_Chained_Trl_Base_Component')) {
            if ($data->componentClass == $this->_class) {
                //vielleicht flexibler machen?
                return Vps_Component_Data_Root::getInstance()
                    ->getComponentByClass(Vpc_Abstract::getSetting($this->_class, 'masterComponentClass'));
            }
            throw new Vps_Exception_NotYetImplemented();
        } else {
            if ($data) {
                $gen = Vps_Component_Generator_Abstract::getInstances($data, array('generatorFlag'=>'trlBase'));
                return $gen[0]->_getChainedData($data);
            }
            return $data;
        }
    }

    protected function _getChainedChildComponents($parentData, $select)
    {
        if ($p = $select->getPart(Vps_Component_Select::WHERE_CHILD_OF_SAME_PAGE)) {
            $select->whereChildOfSamePage($this->_getChainedData($p));
        }
        return $this->_getChainedGenerator()
            ->getChildData($this->_getChainedData($parentData), $select);
    }

    public function getChildData($parentData, $select = array())
    {
        $ret = array();
        if (is_array($select)) $select = new Vps_Component_Select($select);
        if ($id = $select->getPart(Vps_Component_Select::WHERE_ID)) {
            if ($this->_getChainedGenerator() instanceof Vpc_Root_Category_Generator) {
                $select->whereId(substr($id, 1));
            }
        }
        $slaveData = $select->getPart(Vps_Component_Select::WHERE_CHILD_OF_SAME_PAGE);

        foreach ($this->_getChainedChildComponents($parentData, $select) as $component) {
            if (!$parentData) {
                $pData = $this->_getParentData($component, $slaveData);
            } else {
                $pData = $parentData;
            }
            $data = $this->_createData($pData, $component, $select);
            if ($data) {
                $ret[] = $data;
            }
        }
        return $ret;
    }

    private function _getParentData($chainedData, $slaveData)
    {
        while ($slaveData) {
            if (is_instance_of($slaveData->componentClass, 'Vpc_Chained_Trl_Base_Component')) { //wen nötig stattdessen ein neues flag erstellen
                break;
            }
            $slaveData = $slaveData->parent;
        }

        $c = $chainedData->parent;
        $ids = array();
        while ($c) {
            $pos = max(
                strrpos($c->componentId, '-'),
                strrpos($c->componentId, '_')
            );
            $id = substr($c->componentId, $pos);
            if (is_instance_of($c->componentClass, 'Vpc_Root_TrlRoot_Master_Component')) { //wen nötig stattdessen ein neues erstellen
                break;
            }
            if ((int)$id > 0) $id = '_' . $id;
            $c = $c->parent;
            if ($c) $ids[] = $id;
        }
        $ret = $slaveData;
        foreach (array_reverse($ids) as $id) {
            $ret = $ret->getChildComponent($id);
        }
        return $ret;
    }

    protected function _getIdFromRow($row)
    {
        if (is_numeric($row->componentId)) return $row->componentId;
        return substr($row->componentId, max(strrpos($row->componentId, '-'),strrpos($row->componentId, '_'))+1);
    }

    protected function _formatConfig($parentData, $row)
    {
        $componentClass = $this->_settings['masterComponentsMap'][$row->componentClass];
        $id = $this->_getIdFromRow($row);
        $data = array(
            'componentId' => $parentData->componentId.$this->getIdSeparator().$id,
            'dbId' => $parentData->dbId.$this->getIdSeparator().$id,
            'componentClass' => $componentClass,
            'parent' => $parentData,
            'chained' => $row,
            'isPage' => $row->isPage,
            'isPseudoPage' => $row->isPseudoPage,
        );
        if (isset($row->filename)) {
            $data['filename'] = $row->filename;
        }
        if (isset($row->name)) {
            $data['name'] = $row->name;
        }
        if (isset($row->box)) {
            $data['box'] = $row->box;
        }
        return $data;
    }

    public function getChildIds($parentData, $select = array())
    {
        return $this->_getChainedGenerator()
            ->getChildIds($this->_getChainedData($parentData), $select);
    }

    protected function _getChainedGenerator()
    {
        return Vps_Component_Generator_Abstract
            ::getInstance(Vpc_Abstract::getSetting($this->_class, 'masterComponentClass'), $this->_settings['generator']);
    }

    public function getIdSeparator()
    {
        $ret = $this->_getChainedGenerator()->getIdSeparator();
        if (!$ret) $ret = '_'; //pages generator
        return $ret;
    }

    public function getPriority()
    {
        return $this->_getChainedGenerator()->getPriority();
    }

    public function getBoxes()
    {
        return $this->_getChainedGenerator()->getBoxes();
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $flags = $this->_getChainedGenerator()->getGeneratorFlags();

        $copyFlags = array('showInPageTreeAdmin', 'page', 'pseudoPage', 'box', 'multiBox', 'table', 'static', 'hasHome');
        foreach ($copyFlags as $f) {
            if (isset($flags[$f])) {
                $ret[$f] = $flags[$f];
            }
        }

        if (is_instance_of($this->_class, 'Vpc_Chained_Trl_Base_Component')) {
            $ret['trlBase'] = true;
        }
        return $ret;
    }
}
