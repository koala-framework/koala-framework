<?php
class Kwf_Assets_Defer_JsDependency extends Kwf_Assets_Dependency_Abstract
{
    private $_defer;
    private $_contents;
    public function __construct(Kwf_Assets_ProviderList_Abstract $providerList, $contents, $defer)
    {
        parent::__construct($providerList);
        $this->_contents = $contents;
        $this->_defer = $defer;
    }

    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getDeferLoad()
    {
        return $this->_defer;
    }

    public function getContents()
    {
        return $this->_contents;
    }

    public function __toString()
    {
        return $this->_contents;
    }
}
