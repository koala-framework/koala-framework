<?php
class Vpc_News_Categories_PageFactory extends Vpc_Abstract_TablePageFactory
{
    protected $_tableName = 'Vpc_News_Categories_Model';
    protected $_componentClass = 'Vpc_News_Categories_Category_Component';
    protected $_additionalFactories = array();

    protected function _getWhere()
    {
        $where = array(
            'component_id = ?' => $this->_component->getDbId(),
        );
        if (!$this->_showInvisible()) {
            $where['visible = 1'] = '';
        }

        return $where;
    }

    protected function _getNameByRow($row)
    {
        return $row->category;
    }

}
