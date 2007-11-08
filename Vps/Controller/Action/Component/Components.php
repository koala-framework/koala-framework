<?php
class Vps_Controller_Action_Component_Components extends Vps_Controller_Action
{
    public function indexAction()
    {
        $pageCollection = new Vps_PageCollection_TreeBase(Zend_Registry::get('dao'));
        $components = $this->_getComponents(null, $pageCollection);
        asort($components);
        foreach ($components as $class => $ids) {
            echo $class;
            echo '<br />';
            foreach ($ids as $id) {
                echo "<a href=\"/admin/component/edit/$class/$id\">$id</a>&nbsp;&nbsp;&nbsp;";
            }
            echo '<br /><br />';
        }
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

    private function _getComponentsForPage($component, $return)
    {
        if ($component) {
            $return[get_class($component)][] = $component->getId();
            foreach ($component->getChildComponents() as $c) {
                $return = $this->_getComponentsForPage($c, $return);
            }
        }
        return $return;
    }

    private function _getComponents($page, $pageCollection, $return = array())
    {
        $return = $this->_getComponentsForPage($page, $return);
        foreach ($pageCollection->getChildPages($page) as $cp) {
            $return = $this->_getComponentsForPage($cp, $return);
        }
        return $return;
    }

    public function showAction()
    {
        $component = $this->_getComponent();

        if (is_file('application/views/Component.html')) {
            $this->view->setRenderFile('Component.html');
        } else {
            $this->view->setRenderFile(VPS_PATH . '/views/Component.html');
        }
        $this->view->setScriptPath('application/views');
        $this->view->component = $component->getTemplateVars();
        $this->view->mode = '';
    }

    public function jsonShowAction()
    {
        $component = $this->_getComponent();

        $view = new Vps_View_Smarty();
        if (is_file('application/views/Component.html')) {
            $view->setRenderFile('Component.html');
        } else {
            $view->setRenderFile(VPS_PATH . '/views/Component.html');
        }
        $view->setScriptPath('application/views');
        $view->component = $component->getTemplateVars();
        $view->mode = '';

        $this->view->content = $view->render('');
    }

    public function deleteAction()
    {
        $component = $this->_getComponent();
        if ($component) {
            Vpc_Admin::getInstance($component)->delete($component);
            echo 'Deleted.';
        } else {
            echo 'Component not found.';
        }
    }

    public function setupAction()
    {
        $class = $this->_getParam('class');
        $admin = Vpc_Admin::getInstance($class);
        if ($admin) {
            $admin->setup();
            echo 'Setup executed.';
        } else {
            echo 'Admin-Class not found';
        }
    }

    private function _getComponent()
    {
        $id = $this->_getParam('componentId');
        $pageCollection = new Vps_PageCollection_TreeBase(Zend_Registry::get('dao'));
        $component = $pageCollection->findComponent($id);
        if (!$component) {
            $class = $this->_getParam('class');
            $component = Vpc_Abstract::createInstance(Zend_Registry::get('dao'), $class, $id);
        }
        return $component;
    }

    public function updateAction()
    {
        $components = new Vps_Config_Ini('application/components.ini');
        foreach ($components as $component => $compData) {

            $setupClass = str_replace('_Index', '_Admin', $component);
            if (file_exists('./' . str_replace('_', '/', $setupClass) . '.php')){
                $obj = new $setupClass(Zend_Registry::get('db'));
                $obj->setup();
            }

            $config = call_user_func(array($component, 'getStaticSettings'));
            foreach ($config as $element => $value){
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
