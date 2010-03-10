<?php
class Vpc_Columns_Trl_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        $ret = array();
        $ret['columns'] = array();
        $ret['columns']['controllerUrl'] = $this->getControllerUrl();
        $ret['columns']['title'] = trlVps('Edit {0}', $this->_getSetting('componentName'));
        $ret['columns']['icon'] = $this->_getSetting('componentIcon')->__toString();
        $ret['columns']['xtype'] = 'vpc.columns.trl';
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        $cls = $generators['columns']['component']['columns'];
        $admin = Vpc_Admin::getInstance($cls);
        $ret['columns']['componentConfigs'] = array();
        foreach ($admin->getExtConfig() as $k=>$cfg) {
            $ret['columns']['componentConfigs'][$cls.'-'.$k] = $cfg;
            $ret['columns']['editComponents'][] = array(
                'componentClass' => $cls,
                'type' => $k
            );
        }
        return $ret;
    }
}
