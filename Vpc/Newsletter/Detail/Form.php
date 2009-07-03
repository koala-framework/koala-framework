<?php
class Vpc_Newsletter_Detail_Form extends Vps_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->add(Vpc_Abstract_Form::createChildComponentForm('Vpc_Newsletter_Detail_Component', '-mail'));
    }
}
