<?php
class Vps_Model_MirrorCache_SlowSource_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $model = Vps_Model_Abstract::getInstance('Vps_Model_MirrorCache_SlowSource_TestModel');
        echo $model->countRows();
        exit;
    }
}
