<?php
class Kwf_Form_Cards_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Form_Cards_TopModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_temp = "";

    protected function _initFields()
    {
        $cards = $this->_form->add(new Kwf_Form_Container_Cards('type', trlKwf('Type')))
            ->setValues(array('foo', 'bar'));

        $form = new Kwf_Form_Cards_Foo();
        $card = $cards->add();
        $title = "foo";
        $title = str_replace('.', ' ', $title);
        $card->setTitle($title);
        $card->setName("foo");
        if ($form) $card->add($form)->setServiceType('foo');

        $form = new Kwf_Form_Cards_Bar();
        $card = $cards->add();
        $title = "bar";
        $title = str_replace('.', ' ', $title);
        $card->setTitle($title);
        $card->setName("bar");
        if ($form) $card->add($form)->setServiceType('bar');
    }

    public function indexAction()
    {
        $config = array();
        $config['baseParams']['id'] = $this->_getParam('id');
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $config['assetsType'] = 'Kwf_Form_Cards:Test';
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }

    public function getModelDataAction()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Form_Cards_TopModel');
        $row = $model->getRow(1);
        echo $row->firstname;
        exit;

    }
}

