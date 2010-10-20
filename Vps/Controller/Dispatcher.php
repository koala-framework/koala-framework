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

                //FnF models setzen damit tests nicht in echte tabellen schreiben
                Vps_Component_Cache::getInstance()->setModel(new Vps_Component_Cache_CacheModel());
                Vps_Component_Cache::getInstance()->setMetaModel(new Vps_Component_Cache_CacheMetaModel());
                Vps_Component_Cache::getInstance()->setFieldsModel(new Vps_Component_Cache_CacheFieldsModel());

                Vps_Component_Data_Root::setComponentClass($request->getParam('root'));

                Vps_Registry::get('acl')->getComponentAcl()->allowComponent('guest', null);

                //hick hack, für Vps_Component_Abstract_Admin::getControllerUrl
                Zend_Registry::set('testRootComponentClass', $request->getParam('root'));
            }

            $class = $request->getParam('class');
            $controller = $request->getParam('componentController');
            $controller .= 'Controller';
            if ($controller == 'IndexController') $controller = 'Controller';
            if (($pos = strpos($class, '!')) !== false) {
                $controller = substr($class, $pos + 1) . 'Controller';
                $class = substr($class, 0, $pos);
            }
            $className = Vpc_Admin::getComponentClass($class, $controller);
            if (!$className) {
                throw new Vps_Exception("Controller '$controller' for component '$class' not found");
            }
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
