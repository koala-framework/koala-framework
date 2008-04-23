<?php
class Vpc_Formular_FileUpload_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save' => true);

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_TextField('width', trlVps('Width')))
            ->setWidth(50);
        $this->_form->add(new Vps_Form_Field_TextField('max_size', trlVps('Max. filesize (in kb)')))
            ->setWidth(50);
        $this->_form->add(new Vps_Form_Field_TextField('types_allowed', trlVps('Valid types (e.g. jpg, gif)')))
            ->setWidth(150);
    }

}
