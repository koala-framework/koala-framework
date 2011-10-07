<?php
class Vpc_Box_TitleEditable_Admin extends Vpc_Abstract_Admin
{
    public function getPagePropertiesForm()
    {
        return new Vpc_Box_TitleEditable_Form(null, $this->_class);
    }
}
