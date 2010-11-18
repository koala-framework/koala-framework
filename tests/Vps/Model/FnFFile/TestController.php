<?php
class Vps_Model_FnFFile_TestController extends Vps_Controller_Action
{
    public function readAction()
    {
        echo Vps_Model_Abstract::getInstance('Vps_Model_FnFFile_Model')->getRow(1)->test;
        exit;
    }

    public function writeAction()
    {
        $row = Vps_Model_Abstract::getInstance('Vps_Model_FnFFile_Model')->getRow(1);
        $row->test = 'overwritten';
        $row->save();
        exit;
    }

    public function readAfterDelayAction()
    {
        sleep(3);
        echo Vps_Model_Abstract::getInstance('Vps_Model_FnFFile_Model')->getRow(1)->test;
        exit;
    }
}
