<?php
class Kwf_Component_SharedData_Detail_SharedData_Controller extends Kwf_Controller_Action_Auto_Kwc_Form
{
    protected $_formClass = 'Kwf_Component_SharedData_Detail_SharedData_Form';
    public function indexAction()
    {
        parent::indexAction();
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsPackage = new Kwf_Assets_Package_TestPackage('Kwf_Component_SharedData');
    }
}
