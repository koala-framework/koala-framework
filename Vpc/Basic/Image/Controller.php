<?php
class Vpc_Basic_Image_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save' => true);

    public function preDispatch()
    {
        $this->_form = new Vpc_Basic_Image_Form($this->component);
    }

    public function jsonSaveAction()
    {
        parent::jsonSaveAction();

        //für rte
        $this->view->imageUrl = $this->component->getImageUrl();
        $this->view->size = $this->component->getSetting('size');
    }
}