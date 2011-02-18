<?php
class Vps_Form_CardsRealModels_Form_Firstname extends Vps_Form_CardsRealModels_Form_Abstract
{
    protected $_rowType = 'sibfirst';

    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_TextField('firstname', 'Firstname'));
    }

    public function prepareSave($parentRow, $postData)
    {
        parent::prepareSave($parentRow, $postData);
        $row = $this->_getRowByParentRow($parentRow);
    }
}
