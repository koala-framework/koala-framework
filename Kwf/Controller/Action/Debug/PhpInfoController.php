<?php
class Vps_Controller_Action_Debug_PhpInfoController extends Vps_Controller_Action
{
    public function indexAction()
    {
        phpinfo();
        exit;
    }
}
