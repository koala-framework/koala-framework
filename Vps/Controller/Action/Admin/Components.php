<?php
class Vps_Controller_Action_Admin_Components extends Vps_Controller_Action_AutoGrid
{
    protected $_gridColumns = array(
        array('dataIndex' => 'component_class',
              'header'    => 'Komponente',
              'width'     => 250,
              'editor'    => 'TextField'),
        array('dataIndex' => 'name',
              'header'    => 'Bezeichnung',
              'width'     => 200,
              'editor'    => 'TextField')
    );
    protected $_gridButtons = array('delete' => true, 'add' => true, 'save' => true);
    protected $_gridFilters = array();
    protected $_gridDefaultOrder = 'name';
    protected $_gridTableName = 'Vps_Dao_Vpc';
    
    public function actionAction()
    {
        $config['controllerUrl'] = '/admin/components/';
        $this->view->ext('Vps.Admin.Components.Index', $config);
    }
    
    public function jsonIndexAction()
    {
        $this->actionAction();
    }

}
