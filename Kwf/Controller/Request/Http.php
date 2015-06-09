<?php
class Kwf_Controller_Request_Http extends Zend_Controller_Request_Http
{
    public function getResourceName()
    {
        if ($this->getControllerName() == 'component') {
            $ret = 'kwf_component';
        } else if ($this->getControllerName() == 'component_test') {
            $ret = 'kwf_test';
        } else if ($this->getModuleName() == 'kwf_test') {
            $ret = 'kwf_test';
        } else {
            $ret = strtolower($this->getModuleName().'_'.$this->getControllerName());
            if (substr($ret, 0, 23) == 'vkwf_controller_action_') {
                $ret = str_replace('vkwf_controller_action_', 'vkwf_', $ret);
            } else if (substr($ret, 0, 22) == 'kwf_controller_action_') {
                $ret = str_replace('kwf_controller_action_', 'kwf_', $ret);
            }
        }
        return $ret;
    }
}
