<?php
class Vpc_Basic_LinkTag_Generator extends Vps_Component_Generator_Static
{
    protected $_loadTableFromComponent = true;

    
    protected function _formatConstraints($parentData, $constraints)
    {
        //es gibt exakt eine unterkomponente mit der id 'link'
        if (isset($constraints['id'])) {
            if ($constraints['id'] != '-link') {
                return null;
            }
            unset($constraints['id']); //contraint nicht weiterreichen
        }
        if (isset($constraints['componentClass'])) {
            throw new Vps_Exception("componentClass constraint not supported for LinkTag");
        }
        return parent::_formatConstraints($parentData, $constraints);
    }

    protected function _fetchKeys($parentData, $constraints)
    {
        //es gibt exakt eine unterkomponente mit der id 'link'
        $ret = array();
        $constraints = $this->_formatConstraints($parentData, $constraints);
        if (is_null($constraints)) return array();
        $ret[] = 'link';
        return $ret;
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
        $row = $this->_table->find($parentData->dbId)->current();
        return array(
            'componentId' => $componentId,
            'dbId' => $dbId,
            'componentClass' => $this->_settings['component'][$row->component],
            'parent' => $parentData,
            'isPage' => false
        );
    }
}
