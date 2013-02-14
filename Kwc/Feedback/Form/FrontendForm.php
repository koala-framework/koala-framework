<?php
class Kwc_Feedback_Form_FrontendForm extends Kwf_Form
{
    protected $_model = 'Kwc_Feedback_Model';

    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextArea('text', trl('Text')))
            ->setWidth(420)
            ->setHeight(200);
        $this->add(new Kwf_Form_Field_Static('<div class="footer"></div>'))
            ->setCls('footer');
        $this->add(new Kwf_Form_Field_Static('<a class="closeButton">x</a><div class="clear"></div>'))
            ->setCls('cancel');
    }
}
