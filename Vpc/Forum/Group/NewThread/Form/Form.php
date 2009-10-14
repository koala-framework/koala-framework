<?php
class Vpc_Forum_Group_NewThread_Form_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_TextField('subject', trlVps('Subject')))
            ->setAllowBlank(false)
            ->setLabelWidth(80)
            ->setWidth(200);
        $this->add(Vpc_Abstract_Form::createComponentForm('Vpc_Posts_Write_Form_Component', 'post'))
            ->setIdTemplate('{component_id}_{id}-posts')
            ->setIdTemplateField('component_id');
    }
}
