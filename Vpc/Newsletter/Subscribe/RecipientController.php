<?php
class Vpc_Newsletter_Subscribe_RecipientController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_formName = 'Vpc_Newsletter_EditSubscriber_Form';

    protected function _isAllowedComponent()
    {
        $authData = $this->_getAuthData();
        $class = $this->_getParam('class');
        if (!Vps_Registry::get('acl')->isAllowedComponent($class, $authData)) return false;

        $nlComponentId = $this->_getParam('newsletterComponentId');
        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($nlComponentId, array('ignoreVisible'=>true));
        return Vps_Registry::get('acl')->isAllowedComponentById($nlComponentId, $component->componentClass, $authData);
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

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->subscribe_date = date('Y-m-d H:i:s');
        if ($row->getModel()->hasColumn('activated')) {
            $row->activated = 1;
        }
        $row->newsletter_component_id = $this->_getParam('newsletterComponentId');
    }
}
