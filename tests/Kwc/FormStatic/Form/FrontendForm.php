<?php
class Kwc_FormStatic_Form_FrontendForm extends Kwf_Form
{
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $row->addTo('markus@vivid.kwf');
        $row->setFrom($row->email);
        $row->subject = 'Anfrage von FormStatic test';
    }

    protected function _init()
    {
        $this->setModel(new Kwf_Model_Mail(array('tpl' => 'Kwc_FormStatic_Form_Component')));

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
