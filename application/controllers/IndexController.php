<?php
class IndexController extends Zend_Controller_Action
{
    public function norouteAction()
    {
        echo "IndexController::norouteAction()<br />";
    }

    public function __call($methodName, $args)
    {
        echo "IndexController::__call()<br />";
    }
}
?> 