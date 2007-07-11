<?php
class Vps_Controller_Action_Component_Components extends Vps_Controller_Action
{
    public function indexAction()
    {
        $components = array('Vpc_Simple_Textbox_Index');
        foreach ($components as $component) {
            echo '<a href="show?id=1&class=' . $component . '">' . $component . '</a><br />';   
        }
    }
    
    public function showAction()
    {
        $class = $this->_getParam('class');
        $id = $this->_getParam('id');
        $component = new $class(Zend_Registry::get('dao'), $id, $id);

        $templateVars = $component->getTemplateVars('');
        
        $template = $_SERVER['DOCUMENT_ROOT'] . 'application/views/' . $templateVars['template'];
        if (!is_file($template)) {
            $filename = $this->getFrontController()->getDispatcher()->classToFilename($class);
            $filename = str_replace('/Index.php', '/', $filename);
            $template = VPS_PATH . $filename . '/' . $templateVars['template'];
        }
        $templateVars['template'] = $template;
        
        $this->view->setRenderFile(VPS_PATH . '/views/Component.html');
        $this->view->component = $templateVars;
        $this->view->mode = '';
    }

    public function updateAction()
    {
        $filename = "application/components.ini";    
        $components = new Vps_Config_Ini($filename);
        
        foreach ($components as $component => $compData) {
            try {
                $config = call_user_func(array($component, 'getParams'));  
            } catch (Zend_Exception $e){
                p("Component $component was not found");
            }
            
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
