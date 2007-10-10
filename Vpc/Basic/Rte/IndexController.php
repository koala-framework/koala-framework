<?php
class Vpc_Basic_Rte_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_buttons = array('save'   => true);

    public function _initFields()
    {
        $field = new Vps_Auto_Field_HtmlEditor('text', 'Content');
        foreach ($this->component->getSettings() as $key => $val) {
            if ($key != 'text') {
                $method = 'set' . ucfirst($key);
                $field->$method($val);
            }
        }
        $this->_form->add($field);
    }

}
