<?php
class Vps_Controller_Action_Admin_Page extends Vps_Controller_Action
{
    protected $_auth = true;

    public function actionAction()
    {
        $iniComponents = new Zend_Config_Ini('../application/config.ini', 'components');
        // Todo: Decorators abchecken, ob es sie gibt
        $iniDecorators = new Zend_Config_Ini('../application/config.ini', 'decorators');

        $cfg = array();
        $cfg['pageId'] = $this->getRequest()->getParam('id');
        $cfg['components'] = $iniComponents->components->toArray();
        $cfg['decorators'] = $iniDecorators->decorators->toArray();
        $view = new Vps_View_Smarty_Ext(array('/Vps/Admin/Page/Index.js'), 'Vps.Admin.Page.Index', $cfg);
        $this->getResponse()->appendBody($view->render(''));
    }

    public function ajaxAddParagraphAction()
    {
        $pageId = $this->getRequest()->getParam('pageId');
        $page = Vpc_Abstract::getInstance(Zend_Registry::get('dao'), $pageId);
        $components = $this->_inspectPage($page, 'Vpc_Decorator');
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
        if ($page instanceof Vpc_Decorator) {
            $return['decorator'] = $this;
        } else if ($page instanceof Vpc_Paragraphs) {
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
        $iniComponents = new Zend_Config_Ini('../application/config.ini', 'components');
        $componentNames = $iniComponents->components->toArray();

        $pageId = $this->getRequest()->getParam('pageId');
        $componentId = $this->getRequest()->getParam('node');

        $page = Vpc_Abstract::getInstance(Zend_Registry::get('dao'), $pageId);
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
            while ($component instanceof Vpc_Decorator_Abstract) {
                $d['selectedDecorators'][] = get_class($component);
                $cc = $component->getChildComponents();
                $component = $cc[0];
            }

            $d['id'] = $component->getId();
            if (isset($componentNames[get_class($component)])) {
                $d['text'] = $componentNames[get_class($component)];
            } else {
                $d['text'] = str_replace('Vpc_', '', str_replace('_Index', '', get_class($component)));
            }
            if ($component instanceof Vpc_Paragraphs) {
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

}
