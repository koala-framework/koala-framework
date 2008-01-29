<?php
class Vpc_News_PageFactoryDetails extends Vpc_Abstract_TablePageFactory
{
    protected $_tableName = 'Vpc_News_Model';
    protected $_componentClass = 'Vpc_News_Details_Component';
    protected $_additionalFactories = array();

    protected function _init()
    {
        parent::_init();
        $childComponentClasses = Vpc_Abstract::getSetting(get_class($this->_component), 'childComponentClasses');
        $this->_componentClass = $childComponentClasses['details'];
    }

    protected function _getWhere()
    {
        $where = array(
            'component_id = ?' => $this->_component->getId()
        );
        if (!$this->_showInvisible()) {
            $where['visible = 1'] = '';
        }
        return $where;
    }

    protected function _getNameByRow($row)
    {
        return $row->title;
    }

    public function getChildPageByRow($row)
    {
        $page = parent::getChildPageByRow($row);
        $page->setRow($row);
        return $page;
    }

}
