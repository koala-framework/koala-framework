<?php
class Kwc_Posts_Write_Form_FrontendForm extends Kwf_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_TextArea('content', trlKwfStatic('Create Post')))
            ->setWidth('100%')
            ->setHeight(150)
            ->setAllowBlank(false)
            ->setLabelAlign('top');
    }
}
