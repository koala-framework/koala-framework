<?php
class Vps_Controller_Action_Component_Components extends Vps_Controller_Action
{
    public function indexAction()
    {
        $path = $this->getRequest()->getPathInfo();
        if (substr($path, -1) != '/') { $path .= '/'; }
        $components = $this->_traverseDirectory ('Vpc/');
        foreach (array_reverse($components) as $component) {
            echo '<a href="' . $path . 'show?id=1&class=' . $component . '">' . $component . '</a><br />';
        }
    }
    
    private function _traverseDirectory ($path){
        $return = array();
    foreach ( new DirectoryIterator($path) as $item ){
      if ($item->getFilename() != '.' && $item->getFilename() != '..' && $item->getFilename() != '.svn'){
            if ($item->isDir()){
             $pathNew = "$path$item/";
               $return = array_merge($this->_traverseDirectory($pathNew), $return);
            } else {
                if ($item->getFilename() == 'Index.php'){
            $component = str_replace("/", "_", $item->getPath());
            $component .= "_Index";				
                        $return[] = $component;
             }
          }
        }
      }
      return $return;
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

    public function jsonShowAction()
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
        
        $view = new Vps_View_Smarty();
        $view->setRenderFile(VPS_PATH . '/views/Component.html');
        $view->mode = '';
        $view->component = $templateVars;
        $this->view->content = $view->render('');
        
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

    public function initAction()
    {
    $filename = "application/components.ini";    
    $components = new Vps_Config_Ini($filename);
    $db = Zend_Registry::get('db');
        $config;
    foreach ($components as $component => $compData) {
      try {
          $config = call_user_func(array($component, 'getParams')); 
          p ($component);
      } catch (Zend_Exception $e){
          p("Component $component was not found");
      }   
      //Index wird auf Setup geändert             
      $setup = str_replace("Index", "Setup", $component);  
      //Pfad wird erstellt         
      $setupPath = str_replace("_", "/", $setup);
    
      
        if (file_exists("./$setupPath.php")){
            //Methode zum Erzeugen der Tabelle
            $obj = new $setup($db);
            $obj->setup();                  
          }
          
        //$config = call_user_func(array ($component, 'getParams'));
      foreach ($config as $element => $value){
        if (!$components->checkKeyExists($component, $element)) {
          $components->setValue($component, $element, (string) $value);       
          //p("value changed -> " . $compData->$element);				           
        } else {
            //p("key exists -> " . $element);
        }       
      }            
    }     
        $components->write();
    }    
}
