<?php
class Vpc_Abstract_List_Trl_AdminFullSizeEdit extends Vpc_Abstract_List_Trl_Admin
{
    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['list']['xtype'] = 'vpc.listfullsizeeditpanel';
        $ret['list']['childConfig'] = array();

        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        $cls = $generators['child']['component'];
        if (is_array($cls)) $cls = current($cls);

        $admin = Vpc_Admin::getInstance($cls);
        $ret['list']['componentConfigs'] = array();
        foreach ($admin->getExtConfig() as $k=>$cfg) {
            $ret['list']['componentConfigs'][$cls.'-'.$k] = $cfg;
            $ret['list']['editComponents'][] = array(
                'componentClass' => $cls,
                'type' => $k
            );
        }

        return $ret;
    }
}
