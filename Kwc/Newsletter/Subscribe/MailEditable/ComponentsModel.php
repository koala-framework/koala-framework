<?php
class Kwc_Newsletter_Subscribe_MailEditable_ComponentsModel extends Kwc_Mail_Editable_ComponentsModel
{
    protected function _setData(Kwf_Component_Data $c)
    {
        $ret = parent::_setData($c);
        $ret['preview_controller_url'] = Kwc_Admin::getInstance($c->componentClass)->getControllerUrl('Preview');
        return $ret;
    }
}
