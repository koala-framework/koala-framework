<?php
class Kwc_Mail_Controller extends Kwf_Controller_Action_Auto_Kwc_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->getParam('componentId'), array('ignoreVisible' => true));
        $this->_form->setEmptyTexts($component);
    }
}
