<?php
class E3_Component_Decorator extends E3_Component_Abstract
{
    protected $_decorated;
    function __construct($componentId, E3_Dao $dao)
    {
        parent::__construct($componentId, $dao);

        $row = $dao->getTable('E3_Dao_Decorator')
                ->find($this->getComponentId());

        $componentClass = $dao->getTable('E3_Dao_Components')
                            ->getComponentClass($row->componentId);

        $this->_decorated = new $componentClass($row->componentId, $dao);
    }

    public function getTemplateVars()
    {
        $ret = $this->_decorated->getTemplateVars();
        $ret['color'] = 'blue';
        return $ret;
    }
}
