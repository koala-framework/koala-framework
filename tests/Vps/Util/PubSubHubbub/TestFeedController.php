<?php
class Vps_Util_PubSubHubbub_TestFeedController extends Vps_Controller_Action
{
    public function indexAction()
    {
        header('Content-Type: application/atom+xml');
        echo file_get_contents('/tmp/feed'.(int)$this->_getParam('id'));
        exit;
    }
}
