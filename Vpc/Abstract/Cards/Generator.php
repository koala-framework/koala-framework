<?php
class Vpc_Abstract_Cards_Generator extends Vps_Component_Generator_Static
{
    private $_model;

    protected function _getModel()
    {
        if (!$this->_model) {
            $this->_model = Vpc_Abstract::createModel($this->_class);
        }
        return $this->_model;
    }

    protected function _formatSelect($parentData, $select = array())
    {
        //es gibt exakt eine unterkomponente mit der id 'child'
        if ($select->hasPart(Vps_Component_Select::WHERE_ID)) {
            $select->processed(Vps_Component_Select::WHERE_ID);
            if ($select->getPart(Vps_Component_Select::WHERE_ID) != '-child') {
                return null;
            }
        }

        if ($select->hasPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
            $cc = $select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
            $row = $this->_getModel()->find($parentData->dbId)->current();
            if (!in_array($this->_settings['component'][$row->component], $cc)) return null;
        }
        return parent::_formatSelect($parentData, $select);
    }

    protected function _fetchKeys($parentData, $select)
    {
        //es gibt exakt eine unterkomponente mit der id 'child'
        $ret = array();
        $select = $this->_formatSelect($parentData, $select);
        if (is_null($select)) return array();
        $ret[] = 'child';
        return $ret;
    }

    protected function _acceptKey($key, $select, $parentData)
    {
        return true;
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $componentId = '';
        if ($parentData->componentId) {
            $componentId = $parentData->componentId . $this->_idSeparator;
        }
        $componentId .= $componentKey;
        $dbId = '';
        if ($parentData->dbId) {
            $dbId = $parentData->dbId . $this->_idSeparator;
        }
        $dbId .= $componentKey;
        $row = $this->_getModel()->getRow($parentData->dbId);
        if (!$row) $row = $this->_getModel()->createRow();
        if (!$row->component) $row->component = key($this->getChildComponentClasses()); //sollte eigentlich nicht vorkommen
        return array(
            'componentId' => $componentId,
            'dbId' => $dbId,
            'componentClass' => $this->_settings['component'][$row->component],
            'parent' => $parentData,
            'isPage' => false,
            'isPseudoPage' => false
        );
    }

    public function getStaticChildComponentIds()
    {
        return array($this->_idSeparator.'child');
    }
}
