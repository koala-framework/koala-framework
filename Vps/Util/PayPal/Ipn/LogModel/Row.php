<?php
class Vps_Util_PayPal_Ipn_LogModel_Row extends Vps_Model_Db_Row
{
    protected function _afterInsert()
    {
        parent::_afterInsert();
        $c = Vps_Util_PayPal_Ipn_LogModel::decodeCallback($this->custom);
        if ($c && $c['cb']) {
            if (class_exists($c['cb'])) {
                call_user_func(array($c['cb'], 'processIpn'), $this, $c['data']);
            } else if (Vps_Component_Data_Root::getComponentClass()) {
                $c = Vps_Component_Data_Root::getInstance()->getComponentById($c['cb']);
                if ($c) {
                    $c->getComponent()->processIpn($this, $c['data']);
                }
            }
        }
    }
}
