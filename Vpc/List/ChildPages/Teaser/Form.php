<?php
class Vpc_List_ChildPages_Teaser_Form extends Vps_Form_NonTableForm
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $this->setProperty('class', $class);
        $this->add($this->_getChildForm());
    }

    protected function _getChildForm()
    {
        $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), 'child');
        $form->setIdTemplate('{component_id}-{id}');
        return $form;
    }
}
