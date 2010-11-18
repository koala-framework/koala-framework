<?php
class Vpc_Forum_Thread_Edit_Form_FrontendForm extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_TextField('subject', trlVps('Subject')))
            ->setAllowBlank(false)
            ->setWidth(200)
            ->setLabelAlign('top');
        $form = new Vpc_Forum_Thread_Edit_Form_EditForm('form', 'Vpc_Posts_Detail_Edit_Form_Component');
        $this->add($form);
    }
}
