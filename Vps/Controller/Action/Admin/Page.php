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
        $files[] = '/Vps/Admin/Page/Tree.js';
        $files[] = '/Vps/Admin/Page/Form.js';
        $files[] = '/Vps/Admin/Page/Index.js';
        $view = new Vps_View_Smarty_Ext($files, 'Vps.Admin.Page.Index', $cfg);
        $this->getResponse()->appendBody($view->render(''));
    }

    public function ajaxCreateParagraphAction()
    {
        $pageId = $this->getRequest()->getParam('pageId');
        $componentClass = $this->getRequest()->getParam('componentClass');
        $componentId = $this->getRequest()->getParam('componentId');
        $parentComponentId = $this->getRequest()->getParam('parentComponentId');
        $page = Vpc_Abstract::getInstance(Zend_Registry::get('dao'), $pageId);
        $parentComponent = $page->findComponent($parentComponentId);
        $component = $page->findComponent($componentId);
        $position = 0;
        if ($component instanceof Vpc_Paragraphs_Index) {
            $paragraphsComponent = $component;
        } else if ($parentComponent instanceof Vpc_Paragraphs_Index) {
            $paragraphsComponent = $parentComponent;
            foreach ($paragraphsComponent->getChildComponents() as $c) {
                $position++;
                if ($c->getId() == $component->getId()) {
                    break;
                }
            }
        } else {
            $this->getResponse()->setOutputFormat('json');
            throw new Vps_Exception('Either component or parentComponent must be an instance of Vpc_Paragraphs_Index');
        }
        $componentId = $paragraphsComponent->createParagraph($componentClass, $position);
        $this->getResponse()->appendJson('parentComponentId', $paragraphsComponent->getId());
        $this->getResponse()->appendJson('componentId', $componentId);
    }

    public function ajaxDeleteParagraphAction()
    {
        $pageId = $this->getRequest()->getParam('pageId');
        $componentId = $this->getRequest()->getParam('componentId');
        $parentComponentId = $this->getRequest()->getParam('parentComponentId');
        $page = Vpc_Abstract::getInstance(Zend_Registry::get('dao'), $pageId);
        $parentComponent = $page->findComponent($parentComponentId);
        if ($parentComponent instanceof Vpc_Paragraphs_Index) {
            $result = $parentComponent->deleteParagraph($componentId);
            $this->getResponse()->appendJson('success', $result);
            if (!$result) {
                $this->getResponse()->appendJson('error', 'Database Error: Couldn\'t delete component.');
            }
        } else {
            $this->getResponse()->setOutputFormat('json');
            throw new Vps_Exception('Either parentComponent must be an instance of Vpc_Paragraphs_Index');
        }
    }

    public function ajaxSaveComponentAction()
    {
        $decorators = $this->getRequest()->getParam('decorators');
        if (!is_array($decorators)) { $decorators = array(); }
        $id = $this->getRequest()->getParam('id');
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Pages');
        $currentDecorators = $table->saveDecorators($id, array_keys($decorators));
        $this->getResponse()->appendJson('componentId', $id);
        $this->getResponse()->appendJson('decorators', $currentDecorators);
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
            if ($component instanceof Vpc_Paragraphs_Index) {
                $d['cls'] = 'paragraphs';
            } else {
                $d['cls'] = 'leaf';
            }
            if (sizeof($d['selectedDecorators']) > 0) {
                $d['cls'] .= '_decorated';
            }
            $d['leaf'] = false;
            $d['class'] = get_class($component);
            $d['isParagraphs'] = ($component instanceof Vpc_Paragraphs_Index);
            $d['expanded'] = true;
            $data[] = $d;
        }

        $body = Zend_Json::encode($data);
        $this->getResponse()->setBody($body);
    }

}
