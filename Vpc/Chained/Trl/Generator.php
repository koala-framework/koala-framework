<?php
class Vpc_Chained_Trl_Generator extends Vps_Component_Generator_Abstract
{
    protected function _init()
    {
        parent::_init();
        $this->_inherits = $this->_getChainedGenerator()->getInherits();
    }

    protected function _getChainedData($data)
    {
        if ($data) {
            if (isset($data->chained)) {
                $data = $data->chained;
            } else {
                $gen = Vps_Component_Generator_Abstract::getInstances($data, array('generatorFlag'=>'trlBase'));
                return $gen[0]->_getChainedData($data);
            }
        }
        return $data;
    }

    protected function _getChainedChildComponents($parentData, $select)
    {
        if ($p = $select->getPart(Vps_Component_Select::WHERE_ON_SAME_PAGE)) {
            $select->whereOnSamePage($this->_getChainedData($p));
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
        foreach ($this->_getChainedChildComponents($parentData, $select) as $c) {
            if (!$parentData) {
                $id = $c->parent->componentId;

                //***HACK***
                $id = str_replace('root-master', 'root-slave', $id); //TODO WICHTIG das muss besser gemacht werden (englische version von deutscher holen)
                //***/HACK***

                $parentData = Vps_Component_Data_Root::getInstance()->getComponentById($id);
            }
            $data = $this->_createData($parentData, $c, $select);
            if ($data) {
                $ret[] = $data;
            }
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
        return $data;
    }

    public function getChildIds($parentData, $select = array())
    {
        return $this->_getChainedGenerator()
            ->getChildIds($this->_getChainedData($parentData), $select);
    }

    private function _getChainedGenerator()
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

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $flags = $this->_getChainedGenerator()->getGeneratorFlags();

        $copyFlags = array('showInPageTreeAdmin', 'page', 'pseudoPage', 'box', 'multiBox');
        foreach ($copyFlags as $f) {
            if (isset($flags[$f])) {
                $ret[$f] = $flags[$f];
            }
        }
        return $ret;
    }
}
