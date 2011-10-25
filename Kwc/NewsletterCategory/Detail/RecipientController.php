<?php
class Kwc_NewsletterCategory_Detail_RecipientController extends Kwc_Newsletter_Detail_RecipientController
{
    protected function _isAllowedComponent()
    {
        return Kwf_Controller_Action::_isAllowedComponent();
    }

    public function preDispatch()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
        $this->_form = new Kwc_NewsletterCategory_EditSubscriber_Form(null, $c->parent->dbId);
        parent::preDispatch();
    }

    protected function _hasPermissions($row, $action)
    {
        $ret = Kwf_Controller_Action_Auto_Form::_hasPermissions($row, $action);
        if ($ret) {
            $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
            if ($row->newsletter_component_id != $c->parent->dbId) {
                return false;
            }
        }
        return $ret;
    }
}
