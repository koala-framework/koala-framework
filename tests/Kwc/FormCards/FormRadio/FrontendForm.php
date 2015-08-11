<?php
class Kwc_FormCards_FormRadio_FrontendForm extends Kwf_Form
{
    protected function _init()
    {
        $this->setModel(new Kwf_Model_FnF());

        $this->add(new Kwf_Form_Field_TextField('fullname', 'Name'))
            ->setAllowBlank(false);

        $cards = $this->fields->add(new Kwf_Form_Container_Cards('type', 'Example'));
        $cards->setCombobox(new Kwf_Form_Field_Radio('type'));
        $cards->getCombobox()->setDefaultValue('foo');
        $card = $cards->add();
        $card->setName('foo');
        $card->setTitle('Foo');
        $card->fields->add(new Kwf_Form_Field_TextField('foo_value', 'Foo'))
            ->setAllowBlank(false);

        $card = $cards->add();
        $card->setName('bar');
        $card->setTitle('Bar');
        $card->fields->add(new Kwf_Form_Field_TextField('bar_value', 'Bar'))
            ->setAllowBlank(false);
        parent::_init();
    }
}
