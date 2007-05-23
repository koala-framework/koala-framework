<?php
class Vps_Controller_Dispatcher extends Zend_Controller_Dispatcher_Standard
{
    private $_isOverwritten = null;
    private $_isComponent = null;

    public function formatControllerName($unformatted)
    {
        if ($this->_isOverwritten()) {
            return parent::formatControllerName($unformatted);
        } else if ($this->_isComponent()) {
            $unformatted = ucfirst($unformatted);
            return $unformatted;
            //return "Vps_Component_$unformatted";
        } else {
            $unformatted = ucfirst($unformatted);
            return "Vps_Controller_Action_$unformatted";
        }
    }

    public function loadClass($className)
    {
        try {
            return parent::loadClass($className);
        } catch (Zend_Exception $e) {
            return $className;
        }
    }

    public function getControllerDirectory($module = null)
    {
        $frontController = $this->getFrontController();
        $request = $frontController->getRequest();
        $path = $request->getPathInfo();
        if ($this->_isComponent()) {
            return parent::getControllerDirectory();
//            return array('default' => '../library/Vps/Component');
        } else if ($this->_isOverwritten()) {
            return parent::getControllerDirectory();
        } else {
            return array('default' => '../library');
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
