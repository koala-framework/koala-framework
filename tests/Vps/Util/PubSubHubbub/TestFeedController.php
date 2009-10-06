<?php
class Vps_Util_PubSubHubbub_TestFeedController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $f = '/tmp/feedRequested'.(int)$this->_getParam('id');
        file_put_contents($f, file_get_contents($f)+1);

        header('Content-Type: application/atom+xml');
        echo file_get_contents('/tmp/feed'.(int)$this->_getParam('id'));
        exit;
    }
}
