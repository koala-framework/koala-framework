<?php
class Kwf_Assets_Dependency_HttpUrl extends Kwf_Assets_Dependency_Abstract
{
    private $_url;
    public function __construct(Kwf_Assets_ProviderList_Abstract $providerList, $url)
    {
        parent::__construct($providerList);
        $this->_url = $url;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function __toString()
    {
        return $this->_url;
    }

    public function getMimeType()
    {
        if (substr($this->_url, -4) == '.css') {
            return 'text/css';
        } else if (substr($this->_url, -3) == '.js') {
            return 'text/javascript';
        } else {
            $path = parse_url($this->_url, PHP_URL_PATH);
            if (substr($path, -4) == '/css') {
                return 'text/css';
            } else if (substr($path, -3) == '/js') {
                return 'text/javascript';
            }
            throw new Kwf_Exception("Unknown file type");
        }
    }

    public function getIdentifier()
    {
        return $this->_url;
    }
}
