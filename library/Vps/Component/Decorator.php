<?php
class Vps_Component_Decorator extends Vps_Component_Abstract
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
            $row = $this->_dao->getTable('Vps_Dao_Decorator')
                    ->find($this->getComponentId(), $this->getPageKey(), $this->getComponentKey())->current();
            $this->_decorated = $this->createComponent('', $row->component_id);
        }
        return $this->_decorated;
    }
    
    public function getComponentInfo()
    {
      return parent::getComponentInfo() + $this->_getDecorated()->getComponentInfo();
    }
    
    public function getChildComponents()
    {
        return array($this->_getDecorated());
    }
}
