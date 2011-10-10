<?php
class Vpc_Newsletter_Detail_RecipientController extends Vpc_Newsletter_Subscribe_RecipientController
{
    protected function _isAllowedComponent()
    {
        return Vps_Controller_Action::_isAllowedComponent();
    }

    protected function _hasPermissions($row, $action)
    {
        $ret = Vps_Controller_Action_Auto_Form::_hasPermissions($row, $action);
        if ($ret) {
            $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
            if ($row->newsletter_component_id != $c->parent->dbId) {
                return false;
            }
        }
        return $ret;
    }
}
