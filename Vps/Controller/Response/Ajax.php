<?php
class Vps_Controller_Response_Ajax extends Zend_Controller_Response_Abstract
{
    private $_jsonBody = array();
    private $_outputFormat = '';
    
    public function appendJson($name, $content = null)
    {
        if (!is_string($name) && !is_array($name)) {
            throw new Zend_Controller_Response_Exception('Invalid body segment key ("' . gettype($name) . '")');
        }

        if (is_array($name)) {
            $this->_jsonBody += $name;
        } else {
            if (isset($this->_jsonBody[$name])) {
                unset($this->_jsonBody[$name]);
            }
            $this->_jsonBody[$name] = $content;
        }
        $this->_outputFormat = 'json';
        
        return $this;
    }

    public function outputBody()
    {
        if ($this->_outputFormat == 'json') {
            $out = $this->_jsonBody;
            foreach ($this->getException() as $exception) {
                $out['exceptions'][] = $exception->__toString();
            }
            if (isset($out['exceptions'])) {
                $out['success'] = false;
            }
            if (!isset($out['success'])) {
                $out['success'] = true;
            }
            echo Zend_Json::encode($out);
        } else {
            foreach ($this->getException() as $exception) {
                p($exception);
            }
            parent::outputBody();
        }
    }

    public function setOutputFormat($format)
    {
        $this->_outputFormat = $format;
    }
}
