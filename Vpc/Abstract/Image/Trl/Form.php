<?php
class Vpc_Abstract_Image_Trl_Form_ImageData extends Vpc_Abstract_Image_Trl_ImageData
{
    public function load($row)
    {
        $src = $this->_getImageUrl($row->component_id);
        if ($src) {
            return "<img src=\"$src\" />";
        }
        return '';
    }
}

class Vpc_Abstract_Image_Trl_Form extends Vpc_Abstract_Composite_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_ShowField('image', trlVps('Master Image')))
            ->setData(new Vpc_Abstract_Image_Trl_Form_ImageData());
        $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Own Image')));
        $fs->setCheckboxToggle(true);
        $fs->setCheckboxName('own_image');
        $fs->add(Vpc_Abstract_Form::createChildComponentForm($this->getClass(), '-image', 'image'));
    }
}
