<?php
class Vps_Media_Headline_Asset implements Vps_Assets_Dynamic_Interface
{
    private $_loader;
    private $_assetsType;
    private $_rootComponent;

    public function __construct(Vps_Assets_Loader $loader, $assetsType, $rootComponent, $arguments)
    {
        $this->_loader = $loader;
        $this->_assetsType = $assetsType;
        $this->_rootComponent = $rootComponent;
    }

    public function getType()
    {
        return 'js';
    }

    public function getContents()
    {
        $ret = array();
        $dep = $this->_loader->getDependencies();
        $language = Zend_Registry::get('trl')->getTargetLanguage();

        foreach ($this->_getFiles() as $file) {
            try {
                $c = $this->_loader->getFileContents($file, $language);
            } catch (Vps_Exception_NotFound $e) {
                throw new Vps_Exception("File not found: $file");
            }
            foreach (Vps_Media_Headline::getHeadlineStyles($c['contents']) as $selector => $styles) {
                if (!in_array($selector, $ret)) {
                    $ret[] = $selector;
                }
            }
        }
        $ret = Zend_Json::encode($ret);
        return "Ext.namespace('Vps.Headline');\n".
            "Vps.Headline.assetsType = '$this->_assetsType';\n".
            "Vps.Headline.selectors = ".$ret;
    }

    private function _getFiles()
    {
        $ret = array();
        $dep = $this->_loader->getDependencies();
        foreach ($dep->getAssetFiles($this->_assetsType, 'css', 'web', $this->_rootComponent) as $file) {
            if (!(substr($file, 0, 8) == 'dynamic/' || substr($file, 0, 7) == 'http://' || substr($file, 0, 8) == 'https://' || substr($file, 0, 1) == '/')) {
                $ret[] = $file;
            }
        }
        return $ret;
    }

    public function getMTimeFiles()
    {
        $ret = array();
        $dep = $this->_loader->getDependencies();
        foreach ($this->_getFiles() as $file) {
            $ret[] = $dep->getAssetPath($file);
        }
        return $ret;
    }

    public function getMTime()
    {
        return null;
    }

    public function getIncludeInAll()
    {
        return true;
    }
}
