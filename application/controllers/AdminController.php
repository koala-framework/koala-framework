<?php
class AdminController extends Zend_Controller_Action
{
    public function indexAction()
    {
        echo "AdminController::indexAction()<br />";
    }

    public function norouteAction()
    {
        echo "AdminController::norouteAction()<br />";
    }

    public function __call($methodName, $args)
    {
        echo "AdminController::__call()<br />";
    }
}
?>