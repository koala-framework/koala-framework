<?php
class Vpc_List_ChildPages_Teaser_TeaserImage_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $fs = $this->fields->add(new Vps_Form_Container_FieldSet(trlVps('Status')));
        $fs->add(new Vps_Form_Field_Checkbox('visible'))
            ->setFieldLabel(trlVps('Visible'));
        $fs->add(new Vps_Form_Field_TextField('link_text'))
            ->setFieldLabel(trlVps('Link text'))
            ->setWidth(300);

        $fs = $this->fields->add(new Vps_Form_Container_FieldSet(trlVps('Image')));
        $fs->add(Vpc_Abstract_Form::createChildComponentForm($this->getClass(), 'image'));

        $fs = $this->fields->add(new Vps_Form_Container_FieldSet(trlVps('Teaser')));
        $fs->add(Vpc_Abstract_Form::createChildComponentForm($this->getClass(), 'text'));
    }
}
