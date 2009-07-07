<?php
class Vpc_Newsletter_Detail_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $mailClass = Vpc_Abstract::getChildComponentClass($this->_class, 'mail');
        $mailContentClass = Vpc_Abstract::getChildComponentClass($mailClass, 'content');
        $cfg = Vpc_Admin::getInstance($mailContentClass)->getExtConfig();

        $ret['form'] = array_merge($ret['form'], array(
            'xtype' => 'vps.tabpanel',
            'tabs' => array(
                'settings' => array(
                    'xtype'                 => 'vps.autoform',
                    'controllerUrl'         => $this->getControllerUrl(),
                    'title'                 => trlVps('Settings')
                ),
                'mail' => array(
                    'xtype'                 => 'vps.component',
                    'componentEditUrl'      => '/admin/component/edit',
                    'mainComponentClass'    => $mailContentClass,
                    'componentIdSuffix'     => '-mail-content',
                    'componentConfigs'      => array($mailContentClass . '-paragraphs' => $cfg['paragraphs']),
                    'mainEditComponents'    => array(array(
                        'componentClass' => $mailContentClass,
                        'type' => 'paragraphs'
                    )),
                    'mainType'              => 'paragraphs',
                    'title'                 => trlVps('Mail')
                ),
                'recipients' => array(
                    'xtype'                 => 'vpc.newsletter.recipients',
                    'controllerUrl'         => $this->getControllerUrl('Recipients'),
                    'title'                 => trlVps('Recipients')
                ),
                'mailing' => array(
                    'xtype'                 => 'vpc.newsletter.mailing',
                    'controllerUrl'         => $this->getControllerUrl('Mailing'),
                    'title'                 => trlVps('Mailing'),
                    'tbar'                  => array()
                )
            )
        ));

        return $ret;
    }
}
