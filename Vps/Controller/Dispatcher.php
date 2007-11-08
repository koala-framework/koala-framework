<?php
class Vps_Controller_Dispatcher extends Zend_Controller_Dispatcher_Standard
{
    public function getControllerClass(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        if ($module == 'component' && $request->getControllerName() == 'component') {
            $className = '';
            $class = $request->getParam('class');
            Zend_Loader::loadClass($class);
            while ($className == '' && is_subclass_of($class, 'Vpc_Abstract')) {
                if (substr($class, -9) == 'Component') {
                    $class = substr($class, 0, -9);
                }
                if (class_exists($class . 'Controller')) {
                    $className = $class . 'Controller';
                }
                $class = get_parent_class($class);
            }
        } else if ($module == 'component' || $module == 'vps') {
            $className = ucfirst($request->getControllerName());
            $className = "Vps_Controller_Action_Component_$className";
        } else {
            $className = parent::getControllerClass($request);
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
