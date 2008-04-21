<?php
class Vpc_Composite_ParagraphsImage_Controller extends Vps_Controller_Action
{
    public function jsonIndexAction()
    {
        $this->view->vpc(Vpc_Admin::getInstance($this->class)->getExtConfig());
    }
}
