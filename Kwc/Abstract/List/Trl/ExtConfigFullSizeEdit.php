<?php
class Kwc_Abstract_List_Trl_ExtConfigFullSizeEdit extends Kwc_Abstract_List_Trl_ExtConfigList
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['list']['xtype'] = 'kwc.listfullsizeeditpanel';
        $ret['list']['childConfig'] = array();

        $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
        $cls = $generators['child']['component'];
        if (is_array($cls)) $cls = current($cls);

        $admin = Kwc_Admin::getInstance($cls);
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
