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
        $sessionInserted = new Zend_Session_Namespace('test_inserted_value');
        echo ($sessionInserted->value);
        exit;

    }

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
        $sessionInserted = new Zend_Session_Namespace('test_inserted_value');
        $silblingRow = $row->getSilblingRow();
        $sessionInserted->value = $silblingRow->firstname;
    }
}

