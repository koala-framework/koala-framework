<?php
class Kwf_Controller_Action_Debug_SessionRestartController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        Zend_Session::start();
        Zend_Session::destroy();
        Kwf_Util_Redirect::redirect('/');
    }
}
