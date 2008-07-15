<?php
class Vpc_News_Month_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected $_childClassKey = 'detail';
    protected $_uniqueFilename = true;

    protected function _init()
    {
        parent::_init();
        $this->_nameColumn = new Zend_Db_Expr("CONCAT(MONTHNAME(publish_date), ' ', YEAR(publish_date))");
        $this->_filenameColumn = new Zend_Db_Expr("CONCAT(YEAR(publish_date), '_', MONTH(publish_date))");
        $this->_idColumn = new Zend_Db_Expr("CONCAT(YEAR(publish_date), MONTH(publish_date))");
    }

    protected function _buildSelect()
    {
        $select = parent::_buildSelect();
        $select->group(array('YEAR(publish_date)', 'MONTH(publish_date)'));
        $select->order('publish_date DESC');
        return $select;
    }
    protected function _getSelectFields()
    {
        $fields = parent::_getSelectFields();
        $fields['tag'] = new Zend_Db_Expr("CONCAT(YEAR(publish_date), '-', MONTH(publish_date))");
        return $fields;
    }
}
