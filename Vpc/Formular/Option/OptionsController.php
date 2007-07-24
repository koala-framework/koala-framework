<?php
class Vpc_Formular_Option_OptionsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_columns = array(
            array('dataIndex'  => 'value',
                  'hader'      => 'Value',
                  'editor'     => 'TextBox'),
            array('dataIndex'  => 'text',
                  'hader'      => 'Text',
                  'editor'     => 'TextBox'),
            array('dataIndex'  => 'checked',
                  'hader'      => 'Value',
                  'renderer'   => 'Boolean',
                  'editor'     => 'Checkbox'),
                  );

    protected $_buttons = array('save'   => true, 'add' => true, 'delete' => true);
    protected $_tableName = 'Vpc_Formular_Option_OptionsModel';

    /*public function indexAction()
    {
        $this->view->ext('Vps.Auto.Grid');
    }
       
    public function jsonIndexAction()
    {
        $this->indexAction();
    }*/
    
}
