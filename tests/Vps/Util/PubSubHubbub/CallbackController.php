<?php
class Vps_Util_PubSubHubbub_CallbackController extends Vps_Controller_Action
{
    public function indexAction()
    {
        file_put_contents('/tmp/lastCallback'.$this->_getParam('id'), file_get_contents("php://input"));
        Vps_Util_PubSubHubbub::process();
        exit;
    }
}
