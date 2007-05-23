<?php
class Vps_Controller_Action_Page extends Vps_Controller_Action
{
    public function actionAction()
    {
        $iniComponents = new Zend_Config_Ini('../application/config.ini', 'components');
        // Todo: Decorators abchecken, ob es sie gibt
        $iniDecorators = new Zend_Config_Ini('../application/config.ini', 'decorators');

        $cfg = array();
        $view = new Vps_View_Smarty('../library/Vps/Controller/Action');
        $cfg['pageId'] = $this->getRequest()->getParam('id');
        $cfg['components'] = $iniComponents->components->toArray();
        $cfg['decorators'] = $iniDecorators->decorators->toArray();
        $view->assign('file', '/files/Vps/Controller/Action/Page.js');
        $view->assign('function', 'Page');
        $view->assign('config', Zend_Json::encode($cfg));
        $body = $view->render('Ext.html');
        $this->getResponse()->appendBody($body);
    }

    public function componentAction()
    {
        $id = $this->getRequest()->getParam('id');
        $component = Vps_Component_Abstract::getInstance(Zend_Registry::get('dao'), $id);
        $component = $component->findComponent($id);
        $action = str_replace('/admin/component', '', $this->getRequest()->getPathInfo());
        if (substr($action, 0, 1) == '/') { $action = substr($action, 1); }
        $controller = substr(get_class($component), 0, strrpos(get_class($component), '_') + 1) . 'Controller';
        try {
            Zend_Loader::LoadClass($controller);
            $this->_forward($action, $controller, 'component', $this->getRequest()->getParams());
        } catch (Zend_Exception $e) {
            $this->getResponse()->setBody('Editing does not exist for this component. Try Frontend-Editing instead.');
        }
    }

    public function ajaxAddParagraphAction()
    {
        $pageId = $this->getRequest()->getParam('pageId');
        $page = Vps_Component_Abstract::getInstance(Zend_Registry::get('dao'), $pageId);
        $components = $this->_inspectPage($page, 'Vps_Component_Decorator');
        //p($components);
    }

    public function ajaxSaveComponentAction()
    {
        $decorators = $this->getRequest()->getParam('decorators');
        if (!is_array($decorators)) { $decorators = array(); }
        $id = $this->getRequest()->getParam('id');
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
        $table->saveDecorators($id, array_keys($decorators));
    }

    private function _inspectPage($page)
    {
        $return = array();
        if ($page instanceof Vps_Component_Decorator) {
            $return['decorator'] = $this;
        } else if ($page instanceof Vps_Component_Paragraphs) {
            $return['decorator'] = $this;
        } else {
            foreach ($page->getChildComponents() as $childComponent) {
                $return += $childComponent->_inspectPage($page);
            }
        }
        return $return;
    }

    public function ajaxGetNodesAction()
    {
        $pageId = $this->getRequest()->getParam('pageId');
        $componentId = $this->getRequest()->getParam('node');

        $page = Vps_Component_Abstract::getInstance(Zend_Registry::get('dao'), $pageId);
        if ($componentId == 'root') {
            $components = array($page);
        } else {
            $component = $page->findComponent($componentId);
            $components = $component->getChildComponents();
        }

        $data = array();
        foreach ($components as $component) {

            // Decorators nicht anzeigen
            $d['selectedDecorators'] = array();
            while ($component instanceof Vps_Component_Decorator_Abstract) {
                $d['selectedDecorators'][] = get_class($component);
                $component = array_shift($component->getChildComponents());
            }

            $d['id'] = $component->getId();
            $d['text'] = str_replace('Vps_Component_', '', get_class($component));
            if ($component instanceof Vps_Component_Paragraphs) {
                $d['cls'] = 'paragraphs';
            } else {
                $d['cls'] = 'leaf';
            }
            if (sizeof($d['selectedDecorators']) > 0) {
                $d['cls'] .= '_decorated';
            }
            $d['leaf'] = false;
            $d['expanded'] = true;
            $data[] = $d;
        }

        $body = Zend_Json::encode($data);
        $this->getResponse()->setBody($body);
    }

    public function ajaxGetDecorators()
    {

    }

}
