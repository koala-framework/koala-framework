<?php
class Vpc_Composite_ParagraphsImage_Controller extends Vps_Controller_Action
{
    public function indexAction()
    {
       $this->view->ext($this->component);
    }

    public function jsonIndexAction()
    {
        $this->indexAction();
    }
}