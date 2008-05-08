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

            // Zuerst direkt Controller zu Klasse suchen
            if (Vps_Loader::classExists($class . 'Controller')) {
                $className = $class . 'Controller';
            }

            // Wenn nicht gefunden, Vererbungshierarchie durchlaufen
            if ($className == '') {
                Zend_Loader::loadClass($class);
                while (is_subclass_of($class, 'Vps_Component_Abstract')) {
                    $cc = $class;
                    if (substr($cc, -10) == '_Component') {
                        $cc = substr($cc, 0, -10);
                    }
                    $cc .= '_Controller';

                    if (Vps_Loader::classExists($cc)) {
                        return $cc;
                    }
                    $class = get_parent_class($class);
                }
            }

        } else {

            $className = parent::getControllerClass($request);
        }

        return $className;
    }
    
    public function getControllerDirectory($module = null)
    {
        if ($module == 'component') {
            return '';
        } else {
            return parent::getControllerDirectory($module);
        }
    }

    public function loadClass($className)
    {
        if (substr($className, 0, 4) == 'Vpc_' || substr($className, 0, 4) == 'Vps_') {
            try {
                Zend_Loader::loadClass($className);
            } catch (Zend_Exception $e) {
                throw new Zend_Controller_Dispatcher_Exception("Invalid controller class '$className'");
            }
            return $className;
        } else {
            return parent::loadClass($className);
        }
    }
}
