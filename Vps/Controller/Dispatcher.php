<?php
class Vps_Controller_Dispatcher extends Zend_Controller_Dispatcher_Standard
{
    public function getControllerClass(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        if ($module == 'component' && $request->getControllerName() == 'component') {
            
            $className = $this->getFrontController()->getRequest()->getParam('class');
            if (substr($className, -9) == 'Component') {
                $className = substr($className, 0, -9);
            }
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
                            if (substr($class, -9) == 'Component') {
                                $class = substr($class, 0, -9);
                            }
                            if (class_exists($class . 'Controller')) {
                                $className = $class . 'Controller';
                            }
                        } catch (Zend_Exception $e) {
                        }
                        $class = get_parent_class($class);
                    }
                }
            }
        } else if ($module == 'component') {
            $className = ucfirst($request->getControllerName());
            $className = "Vps_Controller_Action_Component_$className";
//             d($className);
        } else {
return parent::getControllerClass($request);
            $className = $request->getControllerName() . 'Controller';
            $controllerDir = $this->getFrontController()->getControllerDirectory();
            $controllerName = $request->getControllerName();
            $controllerFile = $controllerDir['default'] . '/' . $this->classToFilename(parent::formatControllerName($controllerName));

            if (!is_file($controllerFile)) {
                $className = str_replace('Controller', '', ucfirst($className));
                $className = "Vps_Controller_Action_Component_$className";
            }
            
        }

        return $className;
    }

    public function loadClass($className)
    {
        if (substr($className, 0, 32) == 'Vps_Controller_Action_Component_'
            || substr($className, 0, 4) == 'Vpc_') {
            try {
                Zend_Loader::loadClass($className);
            } catch (Zend_Exception $e) {
                throw new Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $className . '")');
            }

            return $className;
        } else {
            return parent::loadClass($className);
        }
    }
}
?>
