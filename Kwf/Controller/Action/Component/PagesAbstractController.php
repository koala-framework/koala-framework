<?php
abstract class Kwf_Controller_Action_Component_PagesAbstractController extends Kwf_Controller_Action_Auto_Tree
{
    protected $_textField = 'name';
    protected $_rootVisible = false;
    protected $_buttons = array();
    protected $_hasPosition = true;
    protected $_modelName = 'Kwf_Component_Model';

    protected function _init()
    {
        $this->_filters->add(new Kwf_Controller_Action_Auto_Filter_Text())
            ->setQueryFields(array('name'));
    }

    public function indexAction()
    {
        $this->view->xtype = 'kwf.component.pages';
    }

    protected function _getUserActionsLogConfig()
    {
        $ret = parent::_getUserActionsLogConfig();
        $ret['componentId'] = $this->_getParam('id');
        $action = $this->getRequest()->getActionName();
        if ($action == 'json-move') {
            $ret['componentId'] = $this->_getParam('source');
        }
        $ret['details'] = trlKwf('Page properties');
        return $ret;
    }

    protected function _formatNode($row)
    {
        $component = $row->getData();
        $data = parent::_formatNode($row);
        $data['uiProvider'] = 'Kwf.Component.PagesNode';

        $nodeConfig = $this->_getNodeConfig($component);
        if (is_null($nodeConfig)) return null;
        $data = array_merge($data, $nodeConfig);
        $icon = $data['icon'];
        if (is_string($icon)) { $icon = new Kwf_Asset($icon); }
        $data['bIcon'] = $icon->toString($data['iconEffects']);
        if (isset($data['icon'])) unset($data['icon']);

        if (!$data['expanded']) {
            $openedNodes = $this->_saveSessionNodeOpened(null, null);
            if ($data['disabled'] && !array_key_exists($row->id, $openedNodes)) {
                $data['expanded'] = true;
            }
        }

        if ($data['loadChildren'] || $data['expanded'] || $data['disabled']) {
            $data['children'] = $this->_formatNodes($component->componentId);
        }

        return $data;
    }

    protected function _getNodeConfig($component)
    {
        return self::getNodeConfig($component);
    }

    public static function getNodeConfig($component)
    {
        if ($component->componentId == 'root') { // Root hat keinen Generator
            $data['icon'] = new Kwf_Asset('world');
            $data['iconEffects'] = array();
            $data['expanded'] = true;
            $data['loadChildren'] = true;
            $data['editControllerComponentId'] = 'root';
            $data['allowDrag'] = false;
            $data['allowDrop'] = false;
            $data['actions'] = array();
        } else {
            $data = $component->generator->getPagesControllerConfig($component);
        }
        $data['disabled'] = false;
        return $data;
    }

    protected function _getParentId($row)
    {
        $parent = $row->parent;
        if (!$parent) return null;
        return $parent->componentId;
    }
}
