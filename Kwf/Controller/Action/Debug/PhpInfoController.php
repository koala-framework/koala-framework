<?php
class Kwf_Controller_Action_Debug_PhpInfoController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        phpinfo();
        exit;
    }
}
