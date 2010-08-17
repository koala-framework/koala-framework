<?php
class Vpc_Root_Category_Cc_GeneratorController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vpc_Root_Category_Cc_GeneratorModel';
    protected $_permissions = array('save');

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_form = new Vpc_Root_Category_Cc_GeneratorForm(null, $this->_getParam('class'));
        $this->_form->setId($this->_getParam('id'));
    }
}
