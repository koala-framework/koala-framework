<?php
class Vpc_Newsletter_Detail_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $mailClass = Vpc_Abstract::getChildComponentClass($this->_class, 'mail');
        $mailClass = Vpc_Abstract::getChildComponentClass($mailClass, 'content');
        $jsClass = Vpc_Admin::getComponentFile($this->_class, 'RecipientsPanel', 'js', true);
        $jsClass = str_replace('_', '.', $jsClass);

        $ret['form'] = array_merge($ret['form'], array(
            'xtype'=>'vpc.newsletter.panel',
            'mailComponentClass' => $mailClass,
            'recipientsControllerUrl' => Vpc_Admin::getInstance($this->_class)->getControllerUrl('Recipients'),
            'mailingControllerUrl' => Vpc_Admin::getInstance($this->_class)->getControllerUrl('Mailing'),
            'recipientsClass' => $jsClass
        ));

        $cfg = Vpc_Admin::getInstance($mailClass)->getExtConfig();
        foreach ($cfg as $k=>$c) {
            $ret['form']['componentConfigs'][$mailClass.'-'.$k] = $c;
            $ret['form']['mainEditComponents'][] = array(
                'componentClass' => $mailClass,
                'type' => $k
            );
            $ret['form']['mainType'] = $k;
        }
        return $ret;
    }
}
