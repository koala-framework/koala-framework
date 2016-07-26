<?php
class Kwc_Box_TitleEditable_Trl_Admin extends Kwc_Abstract_Admin
{
    public function getPagePropertiesForm($config)
    {
        return Kwc_Abstract_Form::createComponentForm($this->_class, 'component');
    }
}
