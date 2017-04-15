<?php
class Kwc_Calendar_Directory_FormController extends Kwc_Directories_Item_Directory_FormController
{
    protected $_defaultOrder = 'from';
    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->add(new Kwf_Form_Field_TextField('title', 'Titel'))
            ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextArea('description', 'Beschreibung'));
        $this->_form->add(new Kwf_Form_Field_DateTimeField('from', 'Start'))
            ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_DateTimeField('to', 'Ende'));
    }
}
