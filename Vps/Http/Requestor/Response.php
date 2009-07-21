<?php
class Vps_Http_Requestor_Response implements Vps_Http_Requestor_Response_Interface
{
    private $_response;

    public function __construct(Zend_Http_Response $response)
    {
        $this->_response = $response;
    }

    public function getBody()
    {
        if (!$this->_response) return '';
        return $this->_response->getBody();
    }

    public function getStatusCode()
    {
        if (!$this->_response) return 500;
        return $this->_response->getStatus();
    }

    public function getContentType()
    {
        if (!$this->_response) return '';
        return $this->_response->getHeader('Content-Type');
    }
}
