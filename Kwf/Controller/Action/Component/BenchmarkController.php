<?php
class Kwf_Controller_Action_Component_BenchmarkController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        header('Location: /kwf/debug/benchmark');
        exit;
    }
}

