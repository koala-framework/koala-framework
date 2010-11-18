<?php
class Vps_Component_SharedData_Detail_SharedData_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_formClass = 'Vps_Component_SharedData_Detail_SharedData_Form';
    public function indexAction()
    {
        parent::indexAction();
        $this->view->viewport = 'Vps.Test.Viewport';
        $this->view->assetsType = 'Vps_Component_SharedData:Test';
    }
}