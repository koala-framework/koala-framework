<?php
                                               //sollte eigentlich abhängig von der verwendeten admin im master von einer untersch. admin erben
class Vpc_Abstract_Composite_Trl_Admin extends Vpc_Abstract_Composite_TabsAdmin
{
    public function getExtConfig()
    {
        $masterAdmin = Vpc_Admin::getInstance(Vpc_Abstract::getSetting($this->_class, 'masterComponentClass'));
        if ($masterAdmin instanceof Vpc_Abstract_Composite_TabsAdmin) {
            $ret = parent::getExtConfig();
        } else {
            $ret = Vpc_Abstract_Composite_Admin::getExtConfig();
        }
        return $ret;
    }
}
