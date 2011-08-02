<?php
class Vps_Http_Requestor_Response_FromArray implements Vps_Http_Requestor_Response_Interface
{
    private $_contents;
    public function __construct(array $contents)
    {
        $this->_contents = $contents;
    }

    public function getBody()
    {
        return $this->_contents['body'];
    }

    public function getContentType()
    {
        return $this->_contents['contentType'];
    }

    public function getHeader($h)
    {
        if ($h=='ETag') {
            return $this->_contents['etag'];
        } else if ($h=='Last-Modified') {
            return $this->_contents['lastModified'];
        }
        return null;
    }

    public function getStatusCode()
    {
        return $this->_contents['statusCode'];
    }

    public function __toString()
    {
        return $this->_contents['body'];
    }
}
