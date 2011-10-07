<?php
class Kwc_NewsletterCategory_EditSubscriber_Component extends Kwc_Newsletter_EditSubscriber_Component
{
    protected function _initForm()
    {
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_NewsletterCategory_Component', array('subroot' => $this->getData()));
        if (!$component) throw new Kwf_Exception('Newsletter-Component not found');
        $this->_form = new Kwc_NewsletterCategory_Subscribe_FrontendForm(
            'form', $component->componentId
        );
        parent::_initForm();
    }
}
