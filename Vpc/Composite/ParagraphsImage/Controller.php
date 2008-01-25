<?php
class Vpc_Composite_ParagraphsImage_Controller extends Vps_Controller_Action
{
    public function indexAction()
    {
        $config = Vpc_Admin::getConfig($this->class, $this->componentId);
        $this->view->vpc($config);
    }

    public function jsonIndexAction()
    {
        $this->indexAction();
    }
}
