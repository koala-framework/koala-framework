<?php
class Kwc_Composite_ParagraphsImage_Controller extends Kwf_Controller_Action
{
    public function jsonIndexAction()
    {
        $this->view->kwc(Kwc_Admin::getInstance($this->_getParam('class'))->getExtConfig());
    }
}
