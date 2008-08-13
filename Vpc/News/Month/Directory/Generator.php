<?php
class Vpc_News_Month_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected $_childClassKey = 'detail';
    protected $_uniqueFilename = true;
    protected $_sqlTimeNames = 'de_DE';

    protected function _init()
    {
        $this->_nameColumn = "nameColumn";
        $this->_filenameColumn = "filenameColumn";
        $this->_idColumn = "idColumn";
        parent::_init();
    }

    public function select($parentData)
    {
        Vps_Registry::get('db')->query("SET lc_time_names = '".($this->_sqlTimeNames)."'");

        $ret = parent::select($parentData->parent);
        $ret->group(array('YEAR(publish_date)', 'MONTH(publish_date)'));
        $ret->order('publish_date DESC');
        return $ret;
    }

    protected function _getSelectFields()
    {
        $ret = parent::_getSelectFields();
        $ret['nameColumn'] = new Zend_Db_Expr("CONCAT(MONTHNAME(publish_date), ' ', YEAR(publish_date))");
        $ret['filenameColumn'] = new Zend_Db_Expr(
            "CONCAT(YEAR(publish_date), '_',
                IF(MONTH(publish_date) < 10, CONCAT('0', MONTH(publish_date)), MONTH(publish_date))
            )"
        );
        $ret['idColumn'] = new Zend_Db_Expr("CONCAT(YEAR(publish_date),
            IF(MONTH(publish_date) < 10, CONCAT('0', MONTH(publish_date)), MONTH(publish_date))
        )");
        return $ret;
    }
}
