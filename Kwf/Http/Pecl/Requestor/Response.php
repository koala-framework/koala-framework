<?php
class Vps_Http_Pecl_Requestor_Response implements Vps_Http_Requestor_Response_Interface
{
    private $_response;

    public function __construct(HttpMessage $response = null)
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
        if (!$this->_response) return -1;
        if ($this->_response->getType() != HTTP_MSG_RESPONSE) return -1;
        return $this->_response->getResponseCode();
    }

    public function getContentType()
    {
        if (!$this->_response) return '';
        if ($this->_response->getType() != HTTP_MSG_RESPONSE) return '';
        return $this->_response->getHeader('Content-Type');
    }

    public function getHeader($h)
    {
        if (!$this->_response) return '';
        if ($this->_response->getType() != HTTP_MSG_RESPONSE) return '';
        return $this->_response->getHeader($h);
    }

    public function __toString()
    {
        if (!$this->_response) return '';
        return $this->_response->__toString();
    }
}