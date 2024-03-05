<?php
abstract class Kwf_Assets_Dependency_Abstract
{
    const DEPENDENCY_TYPE_ALL = 'all';
    const DEPENDENCY_TYPE_REQUIRES = 'requires';
    const DEPENDENCY_TYPE_USES = 'uses';
    const DEPENDENCY_TYPE_COMMONJS = 'commonjs';
    protected $_dependencies = array();
    protected $_providerList;

    public function __construct(Kwf_Assets_ProviderList_Abstract $providerList)
    {
        $this->_providerList = $providerList;
    }

    public function getContentsSource()
    {
        return array(
            'type' => 'contents',
            'contents' => '',
        );
    }

    public function getContentsSourceString()
    {
        $src = $this->getContentsSource();
        if ($src['type'] == 'file') {
            return file_get_contents($src['file']);
        } else if ($src['type'] == 'contents') {
            return $src['contents'];
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
    }

    public function getMimeType()
    {
        return null;
    }

    public function __toString()
    {
        return get_class($this);
    }

    public function getIdentifier()
    {
        throw new Kwf_Exception("getIdentifier is not implemented for '$this'");
    }

    public function getCacheId()
    {
        return $this->getIdentifier();
    }

    public function toDebug()
    {
        return get_class($this).': '.$this->__toString()."\n";
    }
}
