<?php
class Vps_Model_Session_TestController extends Vps_Controller_Action
{
    public function preDispatch()
    {
        //RowObserver brauchen wir hier nicht
        Vps_Component_Data_Root::setComponentClass(false);

        parent::preDispatch();
    }

    public function testExceptionAction()
    {
        throw new Vps_Model_Session_TestException('Test');
    }

    public function modelGetAction()
    {
        $model = Vps_Model_Abstract::getInstance('Vps_Model_Session_TestModel');
        $row = $model->getRow(1);
        echo $row->foo;
        $this->_helper->viewRenderer->setNoRender(true);
    }


    public function modelSetAction()
    {
        $model = Vps_Model_Abstract::getInstance('Vps_Model_Session_TestModel');
        $row = $model->getRow(1);
        if (!$row) throw new Vps_Exception("no row found".print_r($model->getData(), true));
        $row->foo = 'bum';
        $row->save();
        echo "OK";
        $this->_helper->viewRenderer->setNoRender(true);
    }

}
