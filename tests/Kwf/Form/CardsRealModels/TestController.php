<?php
class Kwf_Form_CardsRealModels_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    public function init()
    {
        if ($this->_getParam('testDb')) {
            Kwf_Test_SeparateDb::setDbAndCreateCookie($this->_getParam('testDb'));
        } else {
            Kwf_Test_SeparateDb::setDbFromCookie();
        }
        parent::init();
    }

    protected function _initFields()
    {
        $this->_form = new Kwf_Form_CardsRealModels_Form_Wrapper();
    }

    public function indexAction()
    {
        $config = array();
        $config['baseParams']['id'] = $this->_getParam('id');
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $config['assetsType'] = 'Kwf_Form_CardsRealModels:Test';
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}
