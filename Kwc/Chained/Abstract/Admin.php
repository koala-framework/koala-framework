<?php
class Kwc_Chained_Abstract_Admin extends Kwc_Admin
{
    public function componentToString($component)
    {
        $admin = Kwc_Admin::getInstance(Kwc_Abstract::getSetting($this->_class, 'masterComponentClass'));
        return $admin->componentToString($component->chained);
    }
}