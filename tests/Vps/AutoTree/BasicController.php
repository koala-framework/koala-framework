<?php
class Vps_AutoTree_BasicController extends Vps_Controller_Action_Auto_Tree
{
    protected $_modelName = 'Vps_AutoTree_Model';
    protected $_searchFields = array('name', 'search');

    public function indexAction()
    {
        $this->view->assetsType = 'Vps_AutoTree:Test';
        $this->view->viewport = 'Vps.Test.Viewport';
        parent::indexAction();
    }
}