<?php
class Vps_Form_ComboBoxFilter_RemoteController extends Vps_Controller_Action_Auto_Grid
{
    protected function _initColumns()
    {
        $this->_model = new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'name'=>'test1.1', 'filter_id'=>1),
                array('id'=>2, 'name'=>'test1.2', 'filter_id'=>1),
                array('id'=>3, 'name'=>'test1.3', 'filter_id'=>1),
                array('id'=>4, 'name'=>'test2.4', 'filter_id'=>2),
                array('id'=>5, 'name'=>'test2.5', 'filter_id'=>2),
            )
        ));
        $this->_columns[] = new Vps_Grid_Column('name');
        $this->_columns[] = new Vps_Grid_Column('filter_id');
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_getParam('filter_id')) $ret->whereEquals('filter_id', $this->_getParam('filter_id'));
        return $ret;
    }

    protected function _getResourceName()
    {
        return 'vps_test';
    }
}
