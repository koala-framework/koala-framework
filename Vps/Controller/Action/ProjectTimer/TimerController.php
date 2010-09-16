<?php
class Vps_Controller_Action_ProjectTimer_TimerController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_permissions = array();
    protected $_defaultOrder = array('field' => 'start', 'direction' => 'DESC');
    protected $_paging = 20;
    protected $_model = 'Vps_Util_Model_ProjectTimer';

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Vps_Grid_Column('start', trlVps('Date'), 75))
            ->setRenderer('localizedDate');
        $this->_columns->add(new Vps_Grid_Column('time', trlVps('Time'), 50))
            ->setRenderer('secondsToTime');
        $this->_columns->add(new Vps_Grid_Column('comment', trlVps('Comment'), 340))
            ->setRenderer('nl2br');
    }

    protected function _getSelect()
    {
        $projects = Vps_Model_Abstract::getInstance('Vps_Util_Model_Projects');
        $projectIds = $projects->getApplicationProjectIds();
        if (!$projectIds) return null;

        $ret = parent::_getSelect();
        $ret->whereEquals('project_id', $projectIds);
        return $ret;
    }

    public function indexAction()
    {
        $config = array(
            'timerGridControllerUrl' => $this->getRequest()->getPathInfo(),
            'yearsGridControllerUrl' => '/vps/project-timer/years'
        );
        $this->view->ext('Vps.ProjectTimer.Index', $config);
    }
}
