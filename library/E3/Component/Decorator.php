<?php
class E3_Component_Decorator extends E3_Component_Abstract
{
    protected $_decorated;
    function __construct($componentId, $dao)
    {
        parent::__construct($componentId, $dao);

        $row = $dao->getModel('E3_Model_Decorator')
                ->find($this->getComponentId());

        $componentClass = $dao->getModel('E3_Model_Components')
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
