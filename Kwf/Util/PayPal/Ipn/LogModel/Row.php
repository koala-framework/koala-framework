<?php
class Kwf_Util_PayPal_Ipn_LogModel_Row extends Kwf_Model_Db_Row
{
    protected function _afterInsert()
    {
        parent::_afterInsert();
        $c = Kwf_Util_PayPal_Ipn_LogModel::decodeCallback($this->custom);
        if ($c && $c['cb']) {
            $ret = false;
            if (Kwf_Loader::isValidClass($c['cb'])) {
                $ret = call_user_func(array($c['cb'], 'processIpn'), $this, $c['data']);
            } else if (Kwf_Component_Data_Root::getComponentClass()) {
                $component = Kwf_Component_Data_Root::getInstance()
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
