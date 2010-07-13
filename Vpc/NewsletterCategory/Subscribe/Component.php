<?php
class Vpc_NewsletterCategory_Subscribe_Component extends Vpc_Newsletter_Subscribe_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['mail'] = 'Vpc_NewsletterCategory_Subscribe_Mail_Component';
        return $ret;
    }

    protected function _initForm()
    {
        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_NewsletterCategory_Component', array('subroot' => $this->getData()));
        if (!$component) throw new Vps_Exception('Newsletter-Component not found');
        $this->_form = new Vpc_NewsletterCategory_Subscribe_FrontendForm(
            'form', $component->componentId
        );
    }
}
