<?php
class Vps_AutoTree_BasicController extends Vps_Controller_Action_Auto_Synctree
{
    protected $_modelName = 'Vps_AutoTree_Model';
    protected $_queryFields = array('name', 'search');
    protected $_filters = array('text' => true);

    public function indexAction()
    {
        $this->view->assetsType = 'Vps_AutoTree:Test';
        $this->view->viewport = 'Vps.Test.Viewport';
        parent::indexAction();
    }
}