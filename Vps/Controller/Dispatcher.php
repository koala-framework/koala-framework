<?php
class Vps_Controller_Dispatcher extends Zend_Controller_Dispatcher_Standard
{
    private $_isOverwritten = null;
    private $_isComponent = null;

    public function isDispatchable(Zend_Controller_Request_Abstract $request) {
        return true;
    }

    public function loadClass($className)
    {
        if ($this->_isOverwritten()) {
            return parent::loadClass($className);
        } else if ($this->_isComponent()) {
            $unformatted = str_replace('Controller', '', ucfirst($className));
            return "Vpc_Component_$unformatted";
        } else {
            $unformatted = str_replace('Controller', '', ucfirst($className));
            return "Vps_Controller_Action_$unformatted";
        }
    }

    private function _isOverwritten()
    {
        if (is_null($this->_isOverwritten)) {
            $frontController = $this->getFrontController();
            $request = $frontController->getRequest();
            $controllerDir = $frontController->getControllerDirectory();
            $controllerFile = $controllerDir['default'] . '/' . $this->classToFilename(parent::formatControllerName($request->getControllerName()));
            $this->_isOverwritten = is_file($controllerFile);
        }
        return $this->_isOverwritten;
    }

    private function _isComponent()
    {
        $frontController = $this->getFrontController();
        $request = $frontController->getRequest();
        return $request->getModuleName() == 'component';
    }
}
?>
