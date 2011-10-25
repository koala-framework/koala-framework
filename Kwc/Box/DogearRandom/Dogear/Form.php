<?php
class Kwc_Box_DogearRandom_Dogear_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Colors')))
            ->setHelpText(trlKwf('Color values in Hex code without a leading # (e.g. fa5500)'));

        $fs->add(new Kwf_Form_Field_TextField('color_small_1', trlKwf('Color small 1')))
            ->setWidth(80)
            ->setVtype('alphanum');
        $fs->add(new Kwf_Form_Field_TextField('color_small_2', trlKwf('Color small 2')))
            ->setWidth(80)
            ->setVtype('alphanum');
        $fs->add(new Kwf_Form_Field_TextField('color_big_1', trlKwf('Color big 1')))
            ->setWidth(80)
            ->setVtype('alphanum');
        $fs->add(new Kwf_Form_Field_TextField('color_big_2', trlKwf('Color big 2')))
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
