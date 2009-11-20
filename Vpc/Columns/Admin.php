<?php
class Vpc_Columns_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['form']['xtype'] = 'vpc.columns';
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        $cls = $generators['columns']['component'];
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
