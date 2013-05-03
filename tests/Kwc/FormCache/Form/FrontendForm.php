<?php
class Kwc_FormCache_Form_FrontendForm extends Kwf_Form
{
    protected function _init()
    {
        $this->setModel(new Kwc_FormCache_Form_FormModel());//new Kwf_Model_Mail(array('tpl' => 'Kwc_FormCache_Form_Component')));

        $this->add(new Kwf_Form_Field_TextField('fullname', 'Name'))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('email', 'E-Mail'))
            ->setVtype('email')
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('phone', 'Telefon'))
            ->setWidth(255);
        $this->add(new Kwf_Form_Field_TextArea('content', 'Nachricht'))
            ->setAllowBlank(false);
        parent::_init();
    }
}
