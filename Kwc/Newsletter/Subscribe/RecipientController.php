<?php
class Kwc_Newsletter_Subscribe_RecipientController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_formName = 'Kwc_Newsletter_EditSubscriber_Form';

    public function preDispatch()
    {
        if (!isset($this->_form)) {
            if (isset($this->_formName)) {
                $this->_form = new $this->_formName('form', $this->_getParam('class'), $this->_getParam('newsletterComponentId'));
            }
        }
        parent::preDispatch();
    }

    protected function _isAllowedComponent()
    {
        $authData = $this->_getAuthData();
        $class = $this->_getParam('class');
        if (!Kwf_Registry::get('acl')->isAllowedComponent($class, $authData)) return false;

        $nlComponentId = $this->_getParam('newsletterComponentId');
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($nlComponentId, array('ignoreVisible'=>true));
        return Kwf_Registry::get('acl')->isAllowedComponentById($nlComponentId, $component->componentClass, $authData);
    }

    protected function _hasPermissions($row, $action)
    {
        $ret = parent::_hasPermissions($row, $action);
        if ($ret) {
            if ($row->newsletter_component_id != $this->_getParam('newsletterComponentId')) {
                return false;
            }
        }
        return $ret;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->subscribe_date = date('Y-m-d H:i:s');
        if ($row->getModel()->hasColumn('activated')) {
            $row->activated = 1;
        }
        $row->newsletter_component_id = $this->_getParam('newsletterComponentId');
    }
}
