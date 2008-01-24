<?php
class Vpc_News_PageFactoryDetails extends Vpc_Abstract_TablePageFactory
{
    protected $_tableName = 'Vpc_News_Model';
    protected $_componentClass = 'Vpc_News_Details_Component';
    protected $_additionalFactories = array();

    protected function _getWhere()
    {
        $where = array(
            'page_id = ?' => $this->_component->getDbId(),
            'component_key = ?' => $this->_component->getComponentKey()
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
