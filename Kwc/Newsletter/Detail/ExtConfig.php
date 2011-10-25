<?php
class Kwc_Newsletter_Detail_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Form
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $mailClass = Kwc_Abstract::getChildComponentClass($this->_class, 'mail');
        $mailContentClass = Kwc_Abstract::getChildComponentClass($mailClass, 'content');
        $cfg = Kwc_Admin::getInstance($mailContentClass)->getExtConfig();
        $configs = array();
        $editComponents = array();
        $mainType = null;
        foreach ($cfg as $key => $c) {
            $configs[$mailContentClass . '-' . $key] = $c;
            $editComponents[] = array(
                'componentClass' => $mailContentClass,
                'type' => $key
            );
            if (!$mainType) $mainType = $key;
        }

        $ret['form'] = array_merge($ret['form'], array(
            'xtype' => 'kwf.tabpanel',
            'tabs' => array(
                'mail' => array(
                    'xtype'                 => 'kwf.component',
                    'componentEditUrl'      => '/admin/component/edit',
                    'mainComponentClass'    => $mailContentClass,
                    'componentIdSuffix'     => '-mail-content',
                    'componentConfigs'      => $configs,
                    'mainEditComponents'    => $editComponents,
                    'mainType'              => $mainType,
                    'title'                 => trlKwf('Mail')
                ),
                'recipients' => array(
                    'xtype'                 => 'kwc.newsletter.recipients',
                    'controllerUrl'         => $this->getControllerUrl('Recipients'),
                    'formControllerUrl'     => $this->getControllerUrl('Recipient'),
                    'title'                 => trlKwf('Recipients')
                ),
                'mailing' => array(
                    'xtype'                 => 'kwc.newsletter.mailing',
                    'controllerUrl'         => $this->getControllerUrl('Mailing'),
                    'title'                 => trlKwf('Mailing'),
                    'tbar'                  => array()
                ),
                'statistics' => array(
                    'xtype'                 => 'kwf.autogrid',
                    'controllerUrl'         => $this->getControllerUrl('Statistics'),
                    'title'                 => trlKwf('Statistics')
                )
            )
        ));

        return $ret;
    }
}
