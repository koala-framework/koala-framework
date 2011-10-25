<?php
/**
 * Form mit Edit-Buttons
 */
class Kwc_Abstract_List_ExtConfigEditButton extends Kwf_Component_Abstract_ExtConfig_Form
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['form']['xtype'] = 'kwc.listwitheditbutton';
        $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
        $cls = $generators['child']['component'];
        if (is_array($cls)) $cls = current($cls);

        $admin = Kwc_Admin::getInstance($cls);
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