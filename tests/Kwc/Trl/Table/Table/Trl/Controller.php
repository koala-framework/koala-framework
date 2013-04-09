<?php
class Kwc_Trl_Table_Table_Trl_Controller extends Kwc_Basic_Table_Trl_Controller
{
    public function indexAction()
    {
        parent::indexAction();
        $this->view->viewport = 'Kwf.Test.Viewport';
        $this->view->assetsType = 'Kwc_Trl_Table:Test';
    }
}
