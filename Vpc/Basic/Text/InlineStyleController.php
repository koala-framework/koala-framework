<?php
class Vpc_Basic_Text_InlineStyleController extends Vps_Controller_Action_Auto_Form
{
    protected $_tableName = 'Vpc_Basic_Text_StylesModel';
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_stylesFormName = 'Vpc_Basic_Text_InlineStyleForm';

    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->add(new Vps_Form_Field_TextField('name', trlVps('Name')))
            ->setAllowBlank(false);

        $m = new Vps_Model_Field(array(
            'parentModel' => $this->_form->getModel(),
            'fieldName' => 'styles'
        ));
        $this->_form->add(new $this->_stylesFormName())
                ->setModel($m);
    }
    
    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->tag = 'span';
    }
}
