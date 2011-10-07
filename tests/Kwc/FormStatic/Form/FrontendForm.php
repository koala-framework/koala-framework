<?php
class Vpc_FormStatic_Form_FrontendForm extends Vps_Form
{
    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        $row->addTo('markus@vivid.vps');
        $row->setFrom($row->email);
        $row->subject = 'Anfrage von FormStatic test';
    }

    protected function _init()
    {
        $this->setModel(new Vps_Model_Mail(array('tpl' => 'Vpc_FormStatic_Form_Component')));

        $this->add(new Vps_Form_Field_TextField('fullname', 'Name'))
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_TextField('email', 'E-Mail'))
            ->setVtype('email')
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_TextField('phone', 'Telefon'))
            ->setWidth(255);
        $this->add(new Vps_Form_Field_TextArea('content', 'Nachricht'))
            ->setAllowBlank(false);
        parent::_init();
    }
}
