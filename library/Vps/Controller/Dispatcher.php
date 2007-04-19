<?php
class Vps_Controller_Dispatcher extends Zend_Controller_Dispatcher_Standard
{
    private $_isOverwritten = null;
    
    public function formatControllerName($unformatted)
    {
        if ($this->isOverwritten()) {
            return parent::formatControllerName($unformatted); 
        } else {
            $unformatted = ucfirst($unformatted);
            return "Vps_Controller_Action_$unformatted";
        }
    }

    public function getControllerDirectory($module = null)
    {
        if ($this->isOverwritten()) {
            return parent::getControllerDirectory();
        } else {
            return array('default' => '../library');
        }
    }
    
    private function isOverwritten()
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
}
?>
