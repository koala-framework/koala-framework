<?php
class Kwf_Form_CardsRealModels_Form_Firstname extends Kwf_Form_CardsRealModels_Form_Abstract
{
    protected $_rowType = 'sibfirst';

    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_TextField('firstname', 'Firstname'));
    }
}
