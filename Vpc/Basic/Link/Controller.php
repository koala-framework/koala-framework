<?php
abstract class Vpc_Basic_Link_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function jsonSaveAction()
    {
        parent::jsonSaveAction();

        //für rte
        $this->view->href = get_class($this->component).':'.$this->component->getId();
    }
}
