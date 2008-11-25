<?php
class Vps_Form_ShowField_Storage extends Vps_Form_AddForm
{
    protected $_model = 'Vps_Form_ShowField_ValueOverlapsModel';

    protected function _init()
    {
        parent::_init();

       /* $p = new CourseTypes();
        $this->add(new Vps_Form_Field_TextField('box_nr', 'Box Nr.'));
        if (Zend_Registry::get('userModel')->getAuthedUserRole() != 'partner') {
            $this->add(new Vps_Form_Field_TextField('deposit', 'Deposit'));
            $this->add(new Vps_Form_Field_Checkbox('deposit_returned', 'Deposit returned'));
        }*/
    }


}