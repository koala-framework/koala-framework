<?php
class Vps_Controller_Action_Component_Components extends Vps_Controller_Action
{
    public function indexAction()
    {
        $table = Zend_Registry::get('dao')->getTable('Vps_Dao_Components');
        $path = $this->getRequest()->getPathInfo();
        if (substr($path, -1) != '/') { $path .= '/'; }
        $components = Vpc_Setup_Abstract::getAvailableComponents();
        foreach (array_reverse($components) as $component) {
            
            echo $component . '<br />';
            $rowset = $table->fetchAll("component='$component'");
            $show = array(); $edit = array();
            foreach ($rowset as $row) {
                $show[] = '<a href="/component/show/' . $row->id . '/">' . $row->id . '</a>';
                $edit[] = '<a href="/component/edit/' . $row->id . '/">' . $row->id . '</a>';
            }
            if ($rowset->count() > 0) {
                echo 'edit: ' . implode(',&nbsp;', $edit);
                echo '&nbsp;&nbsp;&nbsp;&nbsp;';
                echo 'show: ' . implode(',&nbsp;', $show);
                echo '<br />';
            }
        }
    }
    
    public function showAction()
    {
        $id = $this->_getParam('id');
        $parts = Vpc_Abstract::parseId($id);
        if ($parts['pageKey'] != '') {
            $component = Vps_PageCollection_Abstract::getInstance()->findComponent($id);
        } else {
            $component = Vpc_Abstract::createInstance(Zend_Registry::get('dao'), $id)->findComponent($id);
        }

        $this->view->setRenderFile(VPS_PATH . '/views/Component.html');
        $this->view->setCompilePath('application/views_c');
        $this->view->setScriptPath('application/views');
        $this->view->setScriptPath('application/views');
        $this->view->component = $component->getTemplateVars('');
        $this->view->mode = '';
    }

    public function jsonShowAction()
    {
        $id = $this->_getParam('id');
        $parts = Vpc_Abstract::parseId($id);
        if ($parts['pageKey'] != '') {
            $component = Vps_PageCollection_Abstract::getInstance()->findComponent($id);
        } else {
            $component = Vpc_Abstract::createInstance(Zend_Registry::get('dao'), $id)->findComponent($id);
        }
        
        $view = new Vps_View_Smarty();
        $view->setRenderFile(VPS_PATH . '/views/Component.html');
        $view->setCompilePath('application/views_c');
        $view->setScriptPath('application/views');
        $view->setScriptPath('application/views');
        $view->component = $component->getTemplateVars('');
        $view->mode = '';

        $this->view->content = $view->render('');
    }

    public function updateAction()
    {
        $components = new Vps_Config_Ini('application/components.ini');
        foreach ($components as $component => $compData) {

            $setupClass = str_replace('_Index', '_Setup', $component);  
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
