<?php
class Vps_AutoTree_BasicController extends Vps_Controller_Action_Auto_Tree
{
    protected $_modelName = 'Vps_AutoTree_Model';

    protected function _init()
    {
        $this->_filters->add(new Vps_Controller_Action_Auto_Filter_Text())
            ->setQueryFields(array('name', 'search'));
        $this->_filters->add(new Vps_Controller_Action_Auto_Filter_ComboBox())
            ->setFieldName('search')
            ->setData(array(array('root', 'root'), array('l1', 'l1'), array('l2', 'l2')));
    }

    public function indexAction()
    {
        $this->view->assetsType = 'Vps_AutoTree:Test';
        $this->view->viewport = 'Vps.Test.Viewport';
        parent::indexAction();
    }
}