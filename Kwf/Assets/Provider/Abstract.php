<?php
abstract class Kwf_Assets_Provider_Abstract
{
    /**
     * @var Kwf_Assets_ProviderList_Abstract
     */
    protected $_providerList;

    protected $_initialized = false;

    public function setProviderList(Kwf_Assets_ProviderList_Abstract $providerList)
    {
        $this->_providerList = $providerList;
    }

    public function getPathTypes()
    {
        return array();
    }
}
