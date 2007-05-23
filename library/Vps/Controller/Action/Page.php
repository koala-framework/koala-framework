<?php
class Vps_Controller_Action_Page extends Vps_Controller_Action
{
    public function actionAction()
    {
        $iniComponents = new Zend_Config_Ini('../application/config.ini', 'components');
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
        $component = Vps_Component_Abstract::getInstance(Zend_Registry::get('dao'), $this->getRequest()->getParam('id'));
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
        p($components);
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
        $id = $this->getRequest()->getParam('node');
        if ($id == 'root') {
            $components[] = Vps_Component_Abstract::getInstance(Zend_Registry::get('dao'), $pageId);
        } else {
            $component = Vps_Component_Abstract::getInstance(Zend_Registry::get('dao'), $id);
            $components = $component->getChildComponents();
        }
        
        $data = array();
        foreach ($components as $component) {

            // Decorators nicht anzeigen
            $d['decorators'] = array();
            while ($component instanceof Vps_Component_Decorator) {
                $d['decorators'][] = get_class($components[0]);
                $childComponents = $component->getChildComponents();
                $component = $childComponents[0];
            }

            $d['id'] = $component->getId();
            $d['text'] = str_replace('Vps_Component_', '', get_class($component));
            if ($component instanceof Vps_Component_Paragraphs) {
                $d['cls'] = 'paragraphs';
            } else {
                $d['cls'] = 'leaf';
            }
            if (sizeof($d['decorators']) > 0) {
                $d['cls'] .= '_decorated';
            }
            $d['leaf'] = false;
            //$d['uiProvider'] = 'MyNodeUI';
            //$d['parentComponentIds'] = $parentComponentIds . ',' . $component->getId();
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
