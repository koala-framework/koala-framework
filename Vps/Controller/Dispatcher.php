<?php
class Vps_Controller_Dispatcher extends Zend_Controller_Dispatcher_Standard
{
    private $_isOverwritten = null;
    private $_isComponent = null;

    public function isDispatchable(Zend_Controller_Request_Abstract $request)
    {
        return true;
    }
    
    public function loadClass($className)
    {
        if ($this->getFrontController()->getRequest()->getModuleName() == 'component') {

            $controllerName = $this->getFrontController()->getRequest()->getControllerName();
            $controllerName = ucfirst($controllerName) . 'Controller';
            $id = $this->getFrontController()->getRequest()->getParam('id');
            $component = Vpc_Abstract::createInstance(Zend_Registry::get('dao'), $id);
            $component = $component->findComponent($id);
            $className = substr(get_class($component), 0, strrpos(get_class($component), '_') + 1) . $controllerName;
            
        } else if ($this->getFrontController()->getRequest()->getModuleName() == 'admin') {
            
            $className = str_replace('Controller', '', ucfirst($className));
            $module = ucfirst($this->getFrontController()->getRequest()->getModuleName());
            if ($module != 'Default' && $module != '') {
                $className = 'Component_' . $className;
            }
            $className = "Vps_Controller_Action_$className";
            
        }

        try {
            Zend_Loader::loadClass($className);
        } catch (Zend_Exception $e) {
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $className . '")');
        }
        
        return $className;
    }

}
?>
