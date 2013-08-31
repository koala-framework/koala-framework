<?php
class Kwf_Controller_Action_Component_BenchmarkController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        Kwf_Util_Redirect::redirect('/kwf/debug/benchmark');
    }
}

