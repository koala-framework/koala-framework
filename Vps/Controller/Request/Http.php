<?php
class Vps_Controller_Request_Http extends Zend_Controller_Request_Http
{
    public function getResourceName()
    {
        if ($this->getControllerName() == 'component') {
            $ret = 'vps_component';
        } else if ($this->getControllerName() == 'component_test') {
            $ret = 'vps_test';
        } else if ($this->getModuleName() == 'vps_test' || $this->getModuleName() == 'web_test') {
            $ret = 'vps_test';
        } else {
            $class = $this->getModuleName().'_'.$this->getControllerName();
            $ret = strtolower(str_replace('vps_controller_action_',
                                            '', $class));
            if (substr($class, 0, 4) == 'vps_') {
                $ret = 'vps_'.$ret;
            }
        }
        return $ret;
    }
}
