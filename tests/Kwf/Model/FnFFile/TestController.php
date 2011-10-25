<?php
class Kwf_Model_FnFFile_TestController extends Kwf_Controller_Action
{
    public function readAction()
    {
        echo Kwf_Model_Abstract::getInstance('Kwf_Model_FnFFile_Model')->getRow(1)->test;
        exit;
    }

    public function writeAction()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwf_Model_FnFFile_Model')->getRow(1);
        $row->test = 'overwritten';
        $row->save();
        exit;
    }

    public function readAfterDelayAction()
    {
        sleep(3);
        echo Kwf_Model_Abstract::getInstance('Kwf_Model_FnFFile_Model')->getRow(1)->test;
        exit;
    }
}
