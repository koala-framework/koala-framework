<?php
class Vpc_Basic_LinkTag_Generator extends Vps_Component_Generator_Static
{
    protected $_loadTableFromComponent = true;

    
    protected function _formatSelect($parentData, $select = array())
    {
        //es gibt exakt eine unterkomponente mit der id 'link'
        if ($select->hasPart(Vps_Component_Select::WHERE_ID)) {
            $select->processed(Vps_Component_Select::WHERE_ID);
            if ($select->getPart(Vps_Component_Select::WHERE_ID) != '-link') {
                return null;
            }
        }
        if ($select->hasPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
            throw new Vps_Exception("componentClass constraint not supported for LinkTag");
        }
        return parent::_formatSelect($parentData, $select);
    }

    protected function _fetchKeys($parentData, $select)
    {
        //es gibt exakt eine unterkomponente mit der id 'link'
        $ret = array();
        $select = $this->_formatSelect($parentData, $select);
        if (is_null($select)) return array();
        $ret[] = 'link';
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
        $row = $this->_getModel()->find($parentData->dbId)->current();
        return array(
            'componentId' => $componentId,
            'dbId' => $dbId,
            'componentClass' => $this->_settings['component'][$row->component],
            'parent' => $parentData,
            'isPage' => false
        );
    }
}
