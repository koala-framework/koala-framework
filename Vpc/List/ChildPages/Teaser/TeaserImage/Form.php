<?php
class Vpc_List_ChildPages_Teaser_TeaserImage_Form extends Vpc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        $fs = $this->fields->add(new Vps_Form_Container_FieldSet(trlVps('Status')));
        $fs->add(new Vps_Form_Field_Checkbox('visible'))
            ->setFieldLabel(trlVps('Visible'));
        $fs->add(new Vps_Form_Field_TextField('link_text'))
            ->setFieldLabel(trlVps('Link text'))
            ->setWidth(300);
        parent::_initFields();
    }
}
