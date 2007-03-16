<?php
class E3_Component_Decorator extends E3_Component_Abstract
{
    protected $_decorated;

    public function getTemplateVars()
    {
        $row = $this->_dao->getTable('E3_Dao_Decorator')
                ->find($this->getComponentId());

        $componentClass = $this->_dao->getTable('E3_Dao_Components')
                            ->getComponentClass($row->componentId);

        $this->_decorated = new $componentClass($row->componentId, $this->_dao);

        $ret['decorated'] = $this->_decorated->getTemplateVars();
        $ret['color'] = 'blue';
        $ret['template'] = 'Decorator.html';
        return $ret;
    }
}
