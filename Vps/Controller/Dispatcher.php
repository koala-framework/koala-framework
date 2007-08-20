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
        $request = $this->getFrontController()->getRequest();
        $module = $request->getModuleName();
        if ($module == 'component') {

            $className = $this->getFrontController()->getRequest()->getParam('class');
            $className .= 'Controller';
            try {
                Zend_Loader::loadClass($className);
            } catch (Zend_Exception $e) {
                $id = $this->getFrontController()->getRequest()->getParam('componentId');
                $pageCollection = Vps_PageCollection_TreeBase::getInstance();
                $componentClass = get_class($pageCollection->findComponent($id));
                if (is_subclass_of($componentClass, 'Vpc_Abstract')) {
                    $class = $componentClass;
                    $className = '';
                    while ($class != 'Vpc_Abstract' && $className == '') {
                        try {
                            if (class_exists($class . 'Controller')) {
                                $className = $class . 'Controller';
                            }
                        } catch (Zend_Exception $e) {
                        }
                        $class = get_parent_class($class);
                    }
                }
            }
            
        } else {

            $controllerDir = $this->getFrontController()->getControllerDirectory();
            $controllerName = $request->getControllerName();
            $controllerFile = $controllerDir['default'] . '/' . $this->classToFilename(parent::formatControllerName($controllerName));

            if (is_file($controllerFile)) {
                require_once($controllerFile);
            } else {
                $className = str_replace('Controller', '', ucfirst($className));
                $className = "Vps_Controller_Action_Component_$className";
            }

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
