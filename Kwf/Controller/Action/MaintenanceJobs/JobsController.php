<?php
class Kwf_Controller_Action_MaintenanceJobs_JobsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('reload');
    protected $_defaultOrder = array('field' => 'last_run', 'direction' => 'DESC');

    public function indexAction()
    {
        $this->view->ext('Kwf.MaintenanceJobs.Index');
    }

    protected function _initColumns()
    {
        parent::_initColumns();

        $model = Kwf_Model_Abstract::getInstance('Kwf_Util_Maintenance_JobRunsModel');

        $data = array();
        foreach (Kwf_Util_Maintenance_Dispatcher::getAllMaintenanceJobIdentifiers() as $jobIdentifier) {
            $job = Kwf_Util_Maintenance_Job_AbstractBase::getInstance($jobIdentifier);

            $s = $model->select();
            $s->whereEquals('job', $jobIdentifier);
            $s->order('start', 'DESC');
            $lastRun = $model->getRow($s);
            $data[] = array(
                'id' => $jobIdentifier,
                'job' => $jobIdentifier,
                'frequency' => $job->getFrequency(),
                'last_run' => $lastRun ? $lastRun->start : null,
                'last_run_status' => $lastRun ? $lastRun->status : null,
            );
        }
        $model = new Kwf_Model_FnF(array(
            'data' => $data
        ));
        $this->setModel($model);
        $this->_columns->add(new Kwf_Grid_Column('job', 'Job', 400));
        $this->_columns->add(new Kwf_Grid_Column('frequency', 'Frequency'));
        $this->_columns->add(new Kwf_Grid_Column_Datetime('last_run', 'Last Run'));
        $this->_columns->add(new Kwf_Grid_Column('last_run_status', 'Last Run Status'));
    }
}

