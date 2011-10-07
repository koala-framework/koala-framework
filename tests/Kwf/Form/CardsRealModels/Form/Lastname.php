<?php
class Vps_Form_CardsRealModels_Form_Lastname extends Vps_Form_CardsRealModels_Form_Abstract
{
    protected $_rowType = 'siblast';

    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_TextField('lastname', 'Lastname'));
    }
}
