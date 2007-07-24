<?php 
class Vpc_Formular_Option_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array(
            array('type'       => 'Checkbox',
                  'fieldLabel' => 'Horizontal',
                  'name'       => 'horizontal')
        );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Option_IndexModel';
    protected $_primaryKey = 'id';
    
    /*public function indexAction()
    {
        $this->view->ext('Vps.Auto.Grid');
    }
       
    public function jsonIndexAction()
    {
        $this->indexAction();
    }*/
}