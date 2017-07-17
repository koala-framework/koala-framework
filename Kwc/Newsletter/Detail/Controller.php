<?php
class Kwc_Newsletter_Detail_Controller extends Kwc_Abstract_Composite_Controller
{
    protected function _initFields()
    {
        parent::_initFields();
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->getParam('componentId'), array('ignoreVisible' => true));
        $this->_form->getByName('mail')->setEmptyTexts($component->getChildComponent('_mail'));
    }
}
