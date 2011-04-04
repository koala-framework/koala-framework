<?php
class Vpc_NewsletterCategory_EditSubscriber_Component extends Vpc_Newsletter_EditSubscriber_Component
{
    protected function _initForm()
    {
        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_NewsletterCategory_Component', array('subroot' => $this->getData()));
        if (!$component) throw new Vps_Exception('Newsletter-Component not found');
        $this->_form = new Vpc_NewsletterCategory_Subscribe_FrontendForm(
            'form', $component->componentId
        );
        parent::_initForm();
    }
}
