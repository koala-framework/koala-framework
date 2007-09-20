<?php
class Vpc_Basic_Image_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_buttons = array (
        'save' => true
    );

    public function indexAction() {
        $this->view->ext('Vpc.Basic.Image.Index');
    }

    public function _initFields()
    {
        $this->_form = new Vps_Auto_Form_Image(null, null, $this->component);
    }
    
    public function jsonLoadAction()
    {
        parent::jsonLoadAction();
        $this->view->urlbig = $this->component->getImageUrl();
        $this->view->url = $this->component->getImageUrl(Vpc_Basic_Image_Index::SIZE_THUMB);
    }
}