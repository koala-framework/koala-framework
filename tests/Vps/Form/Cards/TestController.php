<?php
class Vps_Form_Cards_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Form_Cards_TopModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_temp = "";

    protected function _initFields()
    {
        $cards = $this->_form->add(new Vps_Form_Container_Cards('type', trlVps('Type')))
            ->setValues(array('foo', 'bar'));

        $form = new Vps_Form_Cards_Foo();
        $card = $cards->add();
        $title = "foo";
        $title = str_replace('.', ' ', $title);
        $card->setTitle($title);
        $card->setName("foo");
        if ($form) $card->add($form)->setServiceType('foo');

        $form = new Vps_Form_Cards_Bar();
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
        $this->view->ext('Vps.Auto.FormPanel', $config);
    }

    public function getModelDataAction()
    {
        $model = Vps_Model_Abstract::getInstance('Vps_Form_Cards_TopModel');
        $row = $model->getRow(1);
        echo $row->firstname;
        exit;

    }
}

