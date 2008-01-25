<?php
class Vps_Controller_Action_Component_ComponentsController extends Vps_Controller_Action
{
    public function indexAction()
    {
        $pageCollection = new Vps_PageCollection_TreeBase(Zend_Registry::get('dao'));
        $components = $this->_getComponents(null, $pageCollection);
        asort($components);
        $body = '';
        foreach ($components as $class => $ids) {
            $body .= $class;
            $body .= '<br />';
            foreach ($ids as $id) {
                $body .= "<a href=\"/admin/component/edit/$class?component_id=$id[component_id]\">
                                $id[component_id]</a>&nbsp;&nbsp;&nbsp;";
            }
            $body .= '<br /><br />';
        }
        $this->_helper->viewRenderer->setNoRender();
        $this->getResponse()->appendBody($body);
    }

    private function _showComponents($component, $step = 0)
    {
        if ($component) {
            $url = '/admin/component/edit/' . get_class($component) . '/' . $component->getId();
            echo '<span style="margin-left:' . $step*10 . 'px"></span>';
            echo '<a href="' . $url . '">' . $url . '</a><br />';
            foreach ($component->getChildComponents() as $c) {
                $this->_showComponents($c, $step + 1);
            }
        }
    }

    private function _showPages($page, $pageCollection)
    {
        $this->_showComponents($page);
        echo '<br />';
        foreach ($pageCollection->getChildPages($page) as $cp) {
            $this->_showPages($cp, $pageCollection);
        }
    }

    public function showAction()
    {
        $component = new Vpc_Decorator_Assets_Component(
                            Zend_Registry::get('dao'), $this->_getComponent());

        $this->view->setRenderFile(VPS_PATH . '/views/Component.html');
        $this->view->component = $component->getTemplateVars();
        $this->view->mode = '';
    }

    public function jsonShowAction()
    {
        $component = $this->_getComponent();

        $view = new Vps_View_Smarty();
        $view->setRenderFile(VPS_PATH . '/views/Component.html');
        $view->component = $component->getTemplateVars();
        $view->mode = '';

        $this->view->content = $view->render('');
    }

    private function _getComponent()
    {
        $id = $this->_getParam('componentId');
        if (!$id) {
            $id = $this->_getParam('component_id');
        }
        $pageCollection = new Vps_PageCollection_TreeBase(Zend_Registry::get('dao'));
        $component = $pageCollection->findComponent($id);
        if (!$component) {
            throw new Vps_Controller_Exception('Component not found: ' . $id);
        }
        return $component;
    }

    public function updateAction()
    {
        $components = new Vps_Config_Ini('application/components.ini');
        foreach ($components as $component => $compData) {

            $setupClass = str_replace('_Index', '_Admin', $component);
            if (file_exists('./' . str_replace('_', '/', $setupClass) . '.php')) {
                $obj = new $setupClass(Zend_Registry::get('db'));
                $obj->setup();
            }

            $config = call_user_func(array($component, 'getStaticSettings'));
            foreach ($config as $element => $value) {
                if (!$components->checkKeyExists($component, $element)) {
                    $components->setValue($component, $element, (string) $value);
                    p("value changed -> " . $compData->$element);
                } else {
                    p("key exists -> " . $element);
                }
            }
        }
        $components->write();
    }
}
