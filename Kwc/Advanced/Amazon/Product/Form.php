<?php
class Kwc_Advanced_Amazon_Product_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextField('asin', trlKwf('ASIN')));
        parent::_initFields();
    }
}
