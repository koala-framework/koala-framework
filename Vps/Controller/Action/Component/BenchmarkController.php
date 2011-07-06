<?php
class Vps_Controller_Action_Component_BenchmarkController extends Vps_Controller_Action
{
    public function indexAction()
    {
        header('Location: /admin/debug/benchmark');
        exit;
    }
}
