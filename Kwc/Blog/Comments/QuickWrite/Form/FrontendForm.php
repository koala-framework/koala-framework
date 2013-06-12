<?php
class Kwc_Blog_Comments_QuickWrite_Form_FrontendForm extends Kwc_Posts_Write_Form_FrontendForm
{
    protected function _init()
    {
        parent::_init();
        $this->insertBefore('content', new Kwf_Form_Field_TextField('name', trlKwfStatic('Name')))
            ->setLabelWidth(150)
            ->setAllowBlank(false)
            ->setWidth(200);
        $this->insertAfter('name', new Kwf_Form_Field_TextField('email', trlKwfStatic('E-Mail')))
            ->setLabelWidth(150)
            ->setVtype('email')
            ->setWidth(200);
        $this->fields['content']->setFieldLabel(trlKwfStatic('Comment'));
    }
}
