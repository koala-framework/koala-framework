<?php
class Kwc_Box_TitleEditable_Admin extends Kwc_Abstract_Admin
{
    public function getPagePropertiesForm()
    {
        return new Kwc_Box_TitleEditable_Form(null, $this->_class);
    }
}
