<?php
class Vpc_Menu_Abstract_Controller extends Vps_Controller_Action_Auto_Synctree
{
    protected $_textField = 'name';
    protected $_rootVisible = false;
    protected $_buttons = array();

    public function indexAction()
    {
        parent::indexAction();
        $this->view->baseParams = array(
            'componentId' => $this->_getParam('componentId')
        );
        $cfg = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        foreach ($cfg['form'] as $key => $val) {
            $this->view->$key = $val;
        }
    }

    public function preDispatch()
    {
        $this->_modelName = Vpc_Abstract::getSetting($this->_getParam('class'), 'menuModel');
        parent::preDispatch();

        $root = Vps_Component_Data_Root::getInstance();
        $component = $root->getComponentById($this->_getParam('componentId'));
        $this->_model->setMenuComponent($component);

        $this->_rootParentValue = $component->parent->componentId;
    }

    protected function _formatNode($row)
    {
        $data = parent::_formatNode($row);
        $component = $row->getData();
        $disabled = !Vps_Registry::get('acl')->getComponentAcl()
            ->isAllowed(Zend_Registry::get('userModel')->getAuthedUser(), $component);

        $data['actions'] = array();
        $data['disabled'] = $disabled;
        $data = array_merge($data, $component->generator->getPagesControllerConfig($component));
        if ($disabled) $data['iconEffects'][] = 'forbidden';
        $icon = $data['icon'];
        if (is_string($icon)) {
            $icon = new Vps_Asset($icon);
        }
        $data['bIcon'] = $icon->__toString($data['iconEffects']);
        if (isset($data['icon'])) unset($data['icon']);
        $data['expanded'] = true;

        return $data;
    }
}
