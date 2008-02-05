<?php
class Vpc_Basic_Download_Controller extends Vpc_Abstract_Composite_Controller
{
    public function _initFields()
    {
        parent::_initFields();

        $this->_form->add(new Vps_Auto_Field_TextArea('infotext', 'Infotext'))
            ->setWidth(300)
            ->setGrow(true);
    }
}
