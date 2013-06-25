<?php
class Kwc_Newsletter_Detail_RecipientController extends Kwc_Newsletter_Subscribe_RecipientController
{
    public function preDispatch()
    {
        if (!isset($this->_form)) {
            if (isset($this->_formName)) {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
                $this->_form = new $this->_formName('form', $this->_getParam('class'), $c->parent->dbId);
            }
        }
        parent::preDispatch();
    }
    protected function _isAllowedComponent()
    {
        return Kwf_Controller_Action::_isAllowedComponent();
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
        $row->newsletter_component_id = $c->parent->dbId;
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
