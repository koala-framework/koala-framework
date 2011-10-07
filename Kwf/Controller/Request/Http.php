<?php
class Kwf_Controller_Request_Http extends Zend_Controller_Request_Http
{
    public function getResourceName()
    {
        if ($this->getControllerName() == 'component') {
            $ret = 'kwf_component';
        } else if ($this->getControllerName() == 'component_test') {
            $ret = 'kwf_test';
        } else if ($this->getModuleName() == 'kwf_test' || $this->getModuleName() == 'web_test') {
            $ret = 'kwf_test';
        } else {
            $class = $this->getModuleName().'_'.$this->getControllerName();
            $ret = strtolower(str_replace('kwf_controller_action_',
                                            '', $class));
            if (substr($class, 0, 4) == 'kwf_') {
                $ret = 'kwf_'.$ret;
            }
        }
        return $ret;
    }
}
