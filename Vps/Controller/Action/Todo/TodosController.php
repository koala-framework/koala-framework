<?php
class Vps_Controller_Action_Todo_TodosController extends Vps_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = array('field'=>'id', 'direction'=>'DESC');
    protected $_modelName = 'Vps_Util_Model_Todo';
    protected $_paging = 25;
    protected $_filters = array('text' => true);
    protected $_buttons = array();
    protected $_permissions = array();

    public function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column_Button('info', 'Info', 30))
                ->setButtonIcon('/assets/silkicons/information.png');

        $this->_columns->add(new Vps_Grid_Column('status', trlVps('Status'), 60))
            ->setRenderer('todoStatusIcon');
        $this->_columns->add(new Vps_Grid_Column('priority', trlVps('Priority'), 60));
        $this->_columns->add(new Vps_Grid_Column('estimated_time', trlVps('Estimated time'), 100));
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 250));
        $this->_columns->add(new Vps_Grid_Column('description', trlVps('Description'), 250));
        $this->_columns->add(new Vps_Grid_Column('create_date', trlVps('Create date'), 130));
        $this->_columns->add(new Vps_Grid_Column('deadline', trlVps('Deadline'), 100));
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $projectIds = Vps_Registry::get('config')->todo->projectIds->toArray();
//         $where[] = 'project_id IN('.implode(',', $projectIds).')';
//         $where[] = 'show_online = 1';
        return $where;
    }

    public function jsonOverviewDataAction()
    {
        $row = $this->_model->getRow($this->_getParam('id'))->toArray();
        foreach ($row as $k=>$i) {
            $this->view->$k = $i;
        }
    }

    public function indexAction() {
        $this->view->ext('Vps.Todo.List');
    }
}
