<?php
class Vps_Controller_Action_Trl_VpsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_modelName = "Vps_Trl_Model_Vps";
    protected $_buttons = array();
    protected $_sortable = true;
    protected $_defaultOrder = 'id';
    protected $_paging = 30;
    protected $_editDialog = array('controllerUrl'=>'/vps/trl/vps-edit',
                                   'width'=>600,
                                   'height'=>550);
    protected $_columns;

    protected function _initColumns()
    {
        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width'=>80
        );

        $this->_columns->add(new Vps_Grid_Column_Button('edit'));
        $this->_columns->add(new Vps_Grid_Column('id', 'Id', 50));
        $this->_columns->add(new Vps_Grid_Column('context', 'Context', 100));
        $this->_columns->add(new Vps_Grid_Column('en', 'English Singular', 350));
        $this->_columns->add(new Vps_Grid_Column('en_plural', 'English Plural', 350));

        parent::_initColumns();
    }

   /* public function indexAction ()
    {
        $config = array(
            'controllerUrl' => $this->getRequest()->getPathInfo()
        );
        $this->view->ext('Vps.Trl.Grid', $config);
    }*/
}