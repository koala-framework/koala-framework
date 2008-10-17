<?php
class Vps_Controller_Action_Debug_SessionRestartController extends Vps_Controller_Action
{
    public function indexAction()
    {
        Zend_Session::expireSessionCookie();
        die('ok');
    }

}
