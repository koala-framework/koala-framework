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
            'xtype' => 'kwc.newsletter.detail.tabpanel',
            'tabs' => array(
                'settings' => array(
                    'xtype'                 => 'kwf.autoform',
                    'controllerUrl'         => $this->getControllerUrl(),
                    'title'                 => trlKwf('Settings')
                ),
                'mail' => array(
                    'xtype'                 => 'kwf.component',
                    'componentEditUrl'      => '/admin/component/edit',
                    'mainComponentClass'    => $mailContentClass,
                    'componentIdSuffix'     => '_mail-content',
                    'componentConfigs'      => $configs,
                    'mainEditComponents'    => $editComponents,
                    'mainType'              => $mainType,
                    'title'                 => trlKwf('Mail')
                ),
                'preview' => array(
                    'xtype'                 => 'kwc.newsletter.detail.preview',
                    'controllerUrl'         => $this->getControllerUrl('Preview'),
                    'subscribersControllerUrl' => $this->getControllerUrl('Subscribers'),
                    'authedUserEmail'       => Kwf_Registry::get('userModel')->getAuthedUser() ? Kwf_Registry::get('userModel')->getAuthedUser()->email : '',
                    'title'                 => trlKwf('Preview'),
                    'recipientSources'      => $this->_getRecipientSources()
                ),
                'mailing' => array(
                    'xtype'                 => 'kwc.newsletter.recipients',
                    'title'                 => trlKwf('Mailing'),
                    'recipientsPanel' => array(
                        'title' => trlKwf('Add/Remove Subscriber to Queue'),
                        'controllerUrl' => $this->getControllerUrl('Recipients'),
                        'region' => 'center',
                        'xtype' => 'kwc.newsletter.recipients.grid'
                    ),
                    'recipientsQueuePanel' => array(
                        'title' => trlKwf('Queue'),
                        'controllerUrl' => $this->getControllerUrl('Mailing'),
                        'region' => 'east',
                        'width' => 500,
                        'xtype' => 'kwc.newsletter.recipients.queue'
                    ),
                    'mailingPanel' => array(
                        'title' => trlKwf('Mailing'),
                        'region' => 'south',
                        'controllerUrl' => $this->getControllerUrl('Mailing'),
                        'formControllerUrl' => $this->getControllerUrl('MailingForm'),
                        'xtype' => 'kwc.newsletter.startNewsletter'
                    )
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

    protected function _getRecipientSources()
    {
        $mailClass = (Kwc_Abstract::getChildComponentClass($this->_class, 'mail'));
        return Kwc_Abstract::getSetting($mailClass, 'recipientSources');
    }
}
