<?php
class Vpc_NewsletterCategory_Subscribe_FrontendForm extends Vpc_Newsletter_Subscribe_FrontendForm
{
    protected $_modelName = 'Vpc_NewsletterCategory_Subscribe_Model';

    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Vps_Form_Field_MultiCheckbox('ToPool', 'Pool', trlVps('Categories')))
            ->setPool('Newsletterkategorien')
            ->setWidth(255)
            ->setAllowBlank(false);
    }
}
