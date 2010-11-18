<?php
class Vpc_Forum_Group_NewThread_Form_FrontendForm extends Vps_Form
{
    protected $_modelName = 'Vpc_Forum_Group_Model';

    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_TextField('subject', trlVps('Subject')))
            ->setAllowBlank(false)
            ->setLabelWidth(80)
            ->setWidth(200);
        $subForm = new Vpc_Posts_Write_Form_FrontendForm('post');
        $this->add($subForm)
            ->setIdTemplate('{component_id}_{id}-posts')
            ->setIdTemplateField('component_id');
    }
}
