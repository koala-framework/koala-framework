<?php
class Vpc_Chained_Abstract_Admin extends Vpc_Admin
{
    public function componentToString($component)
    {
        $admin = Vpc_Admin::getInstance(Vpc_Abstract::getSetting($this->_class, 'masterComponentClass'));
        return $admin->componentToString($component->chained);
    }
}