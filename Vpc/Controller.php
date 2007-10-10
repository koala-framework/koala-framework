<?php
class Vpc_Controller extends Vps_Controller_Action
{
    public function indexAction($config = array())
    {
        $class = str_replace('Controller', '', get_class($this));
        $setup = Vpc_Setup::createInstance($class);
        $setup->perfomIndexAction($this->view, $this->component);
    }

}