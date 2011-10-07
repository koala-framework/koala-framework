<?php
class Vps_Util_PayPal_Ipn_LogModel_Row extends Vps_Model_Db_Row
{
    protected function _afterInsert()
    {
        parent::_afterInsert();
        $c = Vps_Util_PayPal_Ipn_LogModel::decodeCallback($this->custom);
        if ($c && $c['cb']) {
            $ret = false;
            if (class_exists($c['cb'])) {
                $ret = call_user_func(array($c['cb'], 'processIpn'), $this, $c['data']);
            } else if (Vps_Component_Data_Root::getComponentClass()) {
                $component = Vps_Component_Data_Root::getInstance()
                                                ->getComponentById($c['cb']);
                if ($component) {
                    $ret = $component->getComponent()->processIpn($this, $c['data']);
                }
            }
            $this->callback_success = $ret;
            $this->save();
        }
    }
}
