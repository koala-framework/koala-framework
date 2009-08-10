<?php
class Vps_Http_Requestor_Response implements Vps_Http_Requestor_Response_Interface
{
    private $_response;

    public function __construct(Zend_Http_Response $response = null)
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

    public function getHeader($h)
    {
        if (!$this->_response) return '';
        return $this->_response->getHeader($h);
    }

    public function __toString()
    {
        if (!$this->_response) return '';
        return $this->_response->getHeadersAsString(true, "\n") . "\n" . $this->getBody();
    }
}
