<?php
class Vpc_Basic_Text_MasterStyleController extends Vpc_Basic_Text_InlineStyleController
{
    protected $_stylesFormName = 'Vpc_Basic_Text_MasterStyleForm';
    protected function init()
    {
        $pattern = Vpc_Abstract::getSetting($this->_getParam('componentClass'),
                                                            'stylesIdPattern');
        if ($pattern) {
            throw new Vps_Exception("You can't edit Master Styles if there is a Pattern");
        }
    }
    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->master = 1;
    }
}
