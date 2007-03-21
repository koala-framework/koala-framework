<?php
class E3_Component_Decorator extends E3_Component_Abstract
{
    protected $_decorated;

    public function getTemplateVars()
    {
        $row = $this->_dao->getTable('E3_Dao_Decorator')
                ->find($this->getComponentId(), $this->getPageKey(), $this->getComponentKey())->current();

        $componentClass = $this->_dao->getTable('E3_Dao_Components')
                            ->getComponentClass($row->component_id);

        $this->_decorated = new $componentClass($this->_dao, $row->component_id);

        $ret['decorated'] = $this->_decorated->getTemplateVars();
        $ret['color'] = 'blue';
        $ret['template'] = 'Decorator.html';
        return $ret;
    }
}
