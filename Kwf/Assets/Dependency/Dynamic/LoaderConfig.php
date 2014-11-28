<?php
class Kwf_Assets_Dependency_Dynamic_LoaderConfig extends Kwf_Assets_Dependency_File
{
    protected $_providerList;

    public function __construct(Kwf_Assets_ProviderList_Abstract $providerList)
    {
        $this->_providerList = $providerList;
        parent::__construct(null);
    }

    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContents($language)
    {
        $config = array(
            'providerList' => get_class($this->_providerList)
        );
        $ret = "";
        $ret .= "if (!window.Kwf) window.Kwf = {};\n";
        $ret .= "if (!window.Kwf.Loader) window.Kwf.Loader = {};\n";
        $ret .= "window.Kwf.Loader.config = ".json_encode($config).";\n";
        return $ret;
    }

    public function getFileName()
    {
        return null;
    }

}
