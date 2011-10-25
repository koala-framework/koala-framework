<?php
class Kwc_Root_Category_Trl_GeneratorController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save');

    protected function _initFields()
    {
        parent::_initFields();
        $this->_form = new Kwc_Root_Category_Trl_GeneratorForm(null, $this->_getParam('class'));
        $this->_form->setId($this->_getParam('id'));
    }
}
