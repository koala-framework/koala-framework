<?php
class E3_Component_Decorator extends E3_Component_Abstract
{
    protected $_decorated;

    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);
        $ret['decorated'] = $this->_getDecorated()->getTemplateVars($mode);
        $ret['color'] = 'blue';
        $ret['template'] = 'Decorator.html';
        return $ret;
    }
    private function _getDecorated()
    {
        if (!isset($this->_decorated)) {
            $row = $this->_dao->getTable('E3_Dao_Decorator')
                    ->find($this->getComponentId(), $this->getPageKey(), $this->getComponentKey())->current();
    
            $componentClass = $this->_dao->getTable('E3_Dao_Components')
                                ->getComponentClass($row->component_id);
    
            $this->_decorated = new $componentClass($this->_dao, $row->component_id);
        }
        return $this->_decorated;
    }
    
    public function getComponentInfo()
    {
    	return parent::getComponentInfo() + $this->_getDecorated()->getComponentInfo();
    }
    
}
