<?php
class Vps_Form_CardsRealModels_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    public function init()
    {
        if ($this->_getParam('testDb')) {
            Vps_Test_SeparateDb::setDbAndCreateCookie($this->_getParam('testDb'));
        } else {
            Vps_Test_SeparateDb::setDbFromCookie();
        }
        parent::init();
    }

    protected function _initFields()
    {
        $this->_form = new Vps_Form_CardsRealModels_Form_Wrapper();
    }

    public function indexAction()
    {
        $config = array();
        $config['baseParams']['id'] = $this->_getParam('id');
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $config['assetsType'] = 'Vps_Form_CardsRealModels:Test';
        $this->view->ext('Vps.Auto.FormPanel', $config, 'Vps.Test.Viewport');
    }
}
