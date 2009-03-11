<?php
class Vpc_Basic_LinkTag_News_NewsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_tableName = 'Vpc_News_Directory_Model';
    //protected $_defaultOrder = 'publish_date';
    protected $_paging = 20;

    protected function _isAllowedComponent()
    {
        return true;
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('title'));
    }
}
