<?php
class Vps_Controller_Response_Ajax extends Zend_Controller_Response_Abstract
{
    private $_jsonBody;
    private $_outputFormat = '';
    public function appendJson($name, $content)
    {
        if (!is_string($name)) {
            require_once 'Zend/Controller/Response/Exception.php';
            throw new Zend_Controller_Response_Exception('Invalid body segment key ("' . gettype($name) . '")');
        }

        if (isset($this->_jsonBody[$name])) {
            unset($this->_jsonBody[$name]);
        }
        $this->_jsonBody[$name] = $content;
        return $this;
    }
    public function outputBody()
    {
        if ($this->_outputFormat == 'json') {

            parent::outputBody();

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
                echo $exception->__toString();
            }
            parent::outputBody();
        }
    }

    public function setOutputFormat($format)
    {
        $this->_outputFormat = $format;
    }
}
