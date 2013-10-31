<?php
class Kwf_Controller_Dispatcher extends Zend_Controller_Dispatcher_Standard
{
    public function getControllerClass(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        if (($module == 'component' && $request->getControllerName() == 'component')
            || ($module == 'component_test' && $request->getControllerName() == 'component_test')
        ) {
            if ($module == 'component_test') {

                //FnF models setzen damit tests nicht in echte tabellen schreiben
                Kwf_Component_Cache::setInstance(Kwf_Component_Cache::CACHE_BACKEND_FNF);

                Kwf_Test_SeparateDb::setDbFromCookie(); // setzt es nur wenn es das cookie wirklich gibt

                if (!Kwf_Loader::isValidClass($request->getParam('root'))) throw new Kwf_Exception_NotFound("Invalid root component");
                Kwf_Component_Data_Root::setComponentClass($request->getParam('root'));

                $root = Kwf_Component_Data_Root::getInstance();
                $root->setFilename('kwf/kwctest/'.$request->getParam('root'));

                Kwf_Registry::get('acl')->getComponentAcl()->allowComponent('guest', null);

                //hick hack, für Kwf_Component_Abstract_Admin::getControllerUrl
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
            if (!in_array($class, Kwc_Abstract::getComponentClasses())) {
                //unknown component class
                return false;
            }
            $className = Kwc_Admin::getComponentClass($class, $controller);
            if (!$className) {
                return false;
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
        // this control make it possible to add an own IndexController in web controllers
        if ($this->_curModule == 'kwf_controller_action_welcome' && $className == 'IndexController' && class_exists('IndexController')) {
            return $className;
        }
        if (substr($className, 0, 5) == 'Vkwf_' || substr($className, 0, 5) == 'Vkwc_'
                || substr($className, 0, 4) == 'Kwc_' || substr($className, 0, 4) == 'Kwf_'
                || $this->_curModule == 'web_test'
        ) {
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
