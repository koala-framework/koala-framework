<?php
class Vpc_Advanced_Amazon_Product_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        $this->add(new Vps_Form_Field_TextField('asin', trlVps('ASIN')));
        parent::_initFields();
    }
}
