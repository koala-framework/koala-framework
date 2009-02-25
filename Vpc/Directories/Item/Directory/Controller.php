<?php
class Vpc_Directories_Item_Directory_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array(
        'save' => true,
        'delete' => true,
        'reload' => true,
        'add'   => true
    );

    protected $_editDialog = array(
        'width' =>  500,
        'height' =>  400
    );

    public function preDispatch()
    {
        parent::preDispatch();
        $formController = Vpc_Admin::getComponentClass($this->_getParam('class'), 'FormController');
        $formController= substr($formController, 0, -10);
        $this->_editDialog['controllerUrl'] = '/admin/component/edit/'.$formController;
    }
}
