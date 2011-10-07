<?php
class Vps_Model_MirrorCache_SlowMirror_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $model = Vps_Model_Abstract::getInstance('Vps_Model_MirrorCache_SlowMirror_TestModel');
        echo $model->countRows();
        exit;
    }
}
