<?php
class Vpc_Box_DogearRandom_Dogear_Form extends Vpc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Colors')))
            ->setHelpText(trlVps('Color values in Hex code without a leading # (e.g. fa5500)'));

        $fs->add(new Vps_Form_Field_TextField('color_small_1', trlVps('Color small 1')))
            ->setWidth(80)
            ->setVtype('alphanum');
        $fs->add(new Vps_Form_Field_TextField('color_small_2', trlVps('Color small 2')))
            ->setWidth(80)
            ->setVtype('alphanum');
        $fs->add(new Vps_Form_Field_TextField('color_big_1', trlVps('Color big 1')))
            ->setWidth(80)
            ->setVtype('alphanum');
        $fs->add(new Vps_Form_Field_TextField('color_big_2', trlVps('Color big 2')))
            ->setWidth(80)
            ->setVtype('alphanum');
    }

    protected function _beforeSave($row)
    {
        if (!$row->color_small_1) $row->color_small_1 = 'FFFFFF';
        if (!$row->color_small_2) $row->color_small_2 = '000000';
        if (!$row->color_big_1) $row->color_big_1 = 'FFFFFF';
        if (!$row->color_big_2) $row->color_big_2 = '000000';
    }
}
