<?php
/**
 * Form mit Edit-Buttons
 */
class Vpc_Abstract_List_ExtConfigEditButton extends Vps_Component_Abstract_ExtConfig_Form
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
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
        $ret['form']['needsComponentPanel'] = true;
        return $ret;
    }
}