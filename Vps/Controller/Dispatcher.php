<?php
class Vps_Controller_Dispatcher extends Zend_Controller_Dispatcher_Standard
{
    public function getControllerClass(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        if ($module == 'component'
                && $request->getControllerName() == 'component') {

            $className = '';
            $class = $request->getParam('class');

            Zend_Loader::loadClass($class);
            while (is_subclass_of($class, 'Vpc_Abstract')) {
                $cc = $class;
                if (substr($cc, -9) == 'Component') {
                    $cc = substr($cc, 0, -9);
                }
                $cc .= 'Controller';
                if (class_exists($cc)) {
                    return $cc;
                }
                $class = get_parent_class($class);
            }

        } else {

            $className = parent::getControllerClass($request);
        }

        return $className;
    }

    public function loadClass($className)
    {
        if (substr($className, 0, 4) == 'Vpc_') {
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
