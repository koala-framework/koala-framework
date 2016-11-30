<?php
class Kwf_Assets_Dependency_EmptyJs extends Kwf_Assets_Dependency_Abstract
{
    private $_identifier;

    public function __construct($identifier, Kwf_Assets_ProviderList_Abstract $providerList)
    {
        parent::__construct($providerList);
        $this->_identifier = $identifier;
    }
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContentsPacked()
    {
        return Kwf_SourceMaps_SourceMap::createEmptyMap('module.exports = {};');
    }

    public function getIdentifier()
    {
        return $this->_identifier;
    }
}
