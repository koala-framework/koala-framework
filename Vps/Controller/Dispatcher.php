<?php
class Vps_Controller_Dispatcher extends Zend_Controller_Dispatcher_Standard
{
    public function getControllerClass(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        if (($module == 'component' && $request->getControllerName() == 'component')
            || ($module == 'component_test' && $request->getControllerName() == 'component_test')
        ) {
            if ($module == 'component_test') {
                Zend_Registry::get('config')->debug->settingsCache = false;
                Zend_Registry::get('config')->debug->componentCache->disable = true;
                Vps_Component_Data_Root::setComponentClass($request->getParam('root'));
                Zend_Registry::set('testRootComponentClass', $request->getParam('root'));
            }

            $class = $request->getParam('class');
            $controller = 'Controller';
            if (($pos = strpos($class, '!')) !== false) {
                $controller = substr($class, $pos + 1) . 'Controller';
                $class = substr($class, 0, $pos);
            }
            $className = Vpc_Admin::getComponentClass($class, $controller);
            Zend_Loader::loadClass($className);

        } else {

            $className = parent::getControllerClass($request);

        }

        return $className;
    }

    public function getControllerDirectory($module = null)
    {
        if ($module == 'component' || $module == 'component_test') {
            return '';
        } else {
            return parent::getControllerDirectory($module);
        }
    }

    public function loadClass($className)
    {
        if (substr($className, 0, 4) == 'Vpc_' || substr($className, 0, 4) == 'Vps_' || $this->_curModule == 'web_test') {
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
