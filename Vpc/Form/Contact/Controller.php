<?php
class Vpc_Formular_Contact_Controller extends Vpc_Formular_Dynamic_Controller
{
    public function init()
    {
        $this->_buttons[] = 'settings';
        parent::init();
    }
}
