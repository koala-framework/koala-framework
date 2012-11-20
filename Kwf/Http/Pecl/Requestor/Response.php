<?php
class Kwf_Http_Pecl_Requestor_Response implements Kwf_Http_Requestor_Response_Interface
{
    private $_response;

    public function __construct(HttpMessage $response = null)
    {
        if ($response && $response->getType() != HTTP_MSG_RESPONSE) {
            throw new Kwf_Exception("invalid response type");
        }
        if ($response && ($response->getResponseCode() == 301 || $response->getResponseCode() == 302)) {
            //workaround for strange pecl_http bug that breaks requests that redirect to the same url again
            //and for some reason then there is only response message containing in the body the second response message (including http headers)
            if (preg_match('#^HTTP/1\.. [0-9]{3} #', $response->getBody())) {
                $r = HttpMessage::factory($response->getBody());
                if ($r->getType() == HTTP_MSG_RESPONSE) {
                    $response = $r;
                }
            }
        }

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
        return $this->_response->getResponseCode();
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
        return $this->_response->__toString();
    }
}
