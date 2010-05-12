<?php
class Vps_AutoTree_BasicController extends Vps_Controller_Action_Auto_Synctree
{
    protected $_modelName = 'Vps_AutoTree_Model';
    protected $_filters = array(
        'foo' => array(
            'type' => 'Text',
            'queryFields' => array('name', 'search')
        ),
        'search' => array(
            'type' => 'ComboBox',
            'data' => array(array('root', 'root'), array('l1', 'l1'), array('l2', 'l2'))
        )
    );

    public function indexAction()
    {
        $this->view->assetsType = 'Vps_AutoTree:Test';
        $this->view->viewport = 'Vps.Test.Viewport';
        parent::indexAction();
    }
}