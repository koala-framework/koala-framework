<?php
class Kwf_Controller_Action_MaintenanceJobs_RunsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_modelName = 'Kwf_Util_Maintenance_JobRunsModel';
    protected $_paging = 25;
    protected $_defaultOrder = array('field' => 'start', 'direction' => 'DESC');

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Kwf_Grid_Column_Datetime('start', 'Start'));
        $this->_columns->add(new Kwf_Grid_Column('status', 'Status', 80));
        $this->_columns->add(new Kwf_Grid_Column('progress', 'Progress', 50));
        $this->_columns->add(new Kwf_Grid_Column('runtime', 'Runtime', 50));
        $this->_columns->add(new Kwf_Grid_Column('log', 'Log', 400));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('job', $this->_getParam('job'));
        return $ret;
    }
}

