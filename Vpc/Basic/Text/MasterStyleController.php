<?php
class Vpc_Basic_Text_MasterStyleController extends Vpc_Basic_Text_InlineStyleController
{
    protected $_stylesFormName = 'Vpc_Basic_Text_MasterStyleForm';

    public function init()
    {
        $pattern = Vpc_Abstract::getSetting($this->_getParam('componentClass'),
                                                            'stylesIdPattern');
        if ($pattern) {
            throw new Vps_Exception("You can't edit Master Styles if there is a Pattern");
        }
        parent::init();
    }
    protected function _initFields()
    {
        parent::_initFields();
        if ($this->_getUserRole() == 'admin') {
            $this->_form->fields->insertAfter('name', new Vps_Form_Field_TextField('tag', trlVps('Selector')));
        } else {
            $this->_form->fields->insertAfter('name', new Vps_Form_Field_ShowField('tag', trlVps('Selector')));
        }
    }


    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->master = 1;
    }
}
