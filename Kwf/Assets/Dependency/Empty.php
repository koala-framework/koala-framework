<?php
class Kwf_Assets_Dependency_Empty extends Kwf_Assets_Dependency_Abstract
{
    private $_mimeType;
    private $_identifier;

    public function __construct($identifier, $mimeType, Kwf_Assets_ProviderList_Abstract $providerList)
    {
        parent::__construct($providerList);
        $this->_mimeType = $mimeType;
        $this->_identifier = $identifier;
    }
    public function getMimeType()
    {
        return $this->_mimeType;
    }

    public function getContentsPacked()
    {
        return Kwf_SourceMaps_SourceMap::createEmptyMap('');
    }

    public function getIdentifier()
    {
        return $this->_identifier;
    }
}
