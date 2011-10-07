<?php
class Kwf_Model_Session_TestController extends Kwf_Controller_Action
{
    public function preDispatch()
    {
        //RowObserver brauchen wir hier nicht
        Kwf_Component_Data_Root::setComponentClass(false);

        parent::preDispatch();
    }

    public function testExceptionAction()
    {
        throw new Kwf_Model_Session_TestException('Test');
    }

    public function modelGetAction()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Model_Session_TestModel');
        $row = $model->getRow(1);
        echo $row->foo;
        $this->_helper->viewRenderer->setNoRender(true);
    }


    public function modelSetAction()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Model_Session_TestModel');
        $row = $model->getRow(1);
        if (!$row) throw new Kwf_Exception("no row found".print_r($model->getData(), true));
        $row->foo = 'bum';
        $row->save();
        echo "OK";
        $this->_helper->viewRenderer->setNoRender(true);
    }

}
