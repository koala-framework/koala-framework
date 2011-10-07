<?php
class Kwf_Form_CardsRealModels_Form_Lastname extends Kwf_Form_CardsRealModels_Form_Abstract
{
    protected $_rowType = 'siblast';

    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_TextField('lastname', 'Lastname'));
    }
}
