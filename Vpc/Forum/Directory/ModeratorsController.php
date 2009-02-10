<?php

class Vpc_Forum_Directory_ModeratorsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_paging = 10;
    protected $_sortable = false;
    protected $_filters = array('text' => true);
    protected $_defaultOrder = 'id';
    protected $_queryFields = array('id', 'firstname', 'lastname', 'email');

    public function preDispatch()
    {
        $this->_model = Zend_Registry::get('userModel');
        parent::preDispatch();
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('id', 'ID', 55));
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 60));
        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('Firstname'), 120));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Lastname'), 120));
        $this->_columns->add(new Vps_Grid_Column('email', trlVps('Email'), 180));
    }
}
