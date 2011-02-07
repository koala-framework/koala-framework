<?php
class Vpc_Abstract_List_AdminWithEditButton extends Vpc_Admin
{
    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['form']['xtype'] = 'vpc.listwitheditbutton';
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        $cls = $generators['child']['component'];
        if (is_array($cls)) $cls = current($cls);

        $admin = Vpc_Admin::getInstance($cls);
        $ret['form']['componentConfigs'] = array();
        foreach ($admin->getExtConfig() as $k=>$cfg) {
            $ret['form']['componentConfigs'][$cls.'-'.$k] = $cfg;
            $ret['form']['editComponents'][] = array(
                'componentClass' => $cls,
                'type' => $k
            );
        }
        return $ret;
    }
}
