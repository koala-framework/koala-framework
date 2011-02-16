<?php
class Vps_Form_CardsRealModels_Form_Details extends Vps_Form
{
    protected $_modelName = 'Vps_Form_CardsRealModels_Model_WrapperModel';

    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_TextArea('comment', 'Comments'));
    }

}
