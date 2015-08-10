<?php
class Kwf_Assets_Components_Dependency_Abstract extends Kwf_Assets_Dependency_Abstract
{
    protected $_componentClass;
    protected $_componentDependencies;
    protected $_isMaster;

    public function __construct($componentClass, array $componentDependencies, $isMaster)
    {
        $this->_componentClass = $componentClass;
        $this->_componentDependencies = $componentDependencies;
        $this->_isMaster = $isMaster;
    }

    private function _getKwcClass()
    {
        $kwcClass = Kwf_Component_Abstract::formatRootElementClass($this->_componentClass, '');
        if ($this->_isMaster) $kwcClass .= 'Master';
        if (Kwf_Config::getValue('application.uniquePrefix')) {
            $kwcClass = str_replace('kwfUp-', Kwf_Config::getValue('application.uniquePrefix').'-', $kwcClass);
        } else {
            $kwcClass = str_replace('kwfUp-', '', $kwcClass);
        }
        return $kwcClass;
    }

    public function getMTime()
    {
        $ret = null;
        foreach ($this->_componentDependencies as $dep) {
            $ret = max($ret, $dep->getMTime());
        }
        return $ret;
    }

    public function getContents($language)
    {
        $ret = '';
        foreach ($this->_componentDependencies as $dep) {
            $c = $dep->getContents($language);
            if (Kwf_Config::getValue('application.uniquePrefix')) {
                $c = str_replace('kwcBem--', $this->_getKwcClass().'--', $c);
                $c = str_replace('kwcBem__', $this->_getKwcClass().'__', $c);
            } else {
                $c = str_replace('kwcBem--', '', $c);
                $c = str_replace('kwcBem__', '', $c);
            }
            $c = str_replace('.kwcClass', '.'.$this->_getKwcClass(), $c);
            $ret .= $c."\n";
        }
        return $ret;
    }

    public function getContentsSourceString()
    {
        $ret = '';
        foreach ($this->_componentDependencies as $dep) {
            $ret .= $dep->getContentsSourceString()."\n";
        }
        $ret;
    }

    public function getContentsSource()
    {
        $ret = '';
        foreach ($this->_componentDependencies as $dep) {
            $s = $dep->getContentsSource();
            if ($s['type'] == 'file') {
                $ret .= file_get_contents($s['file'])."\n";
            } else if ($s['type'] == 'contents') {
                $ret .= $s['contents']."\n";
            } else {
                throw new Kwf_Exception_NotYetImplemented();
            }
        }
        return array(
            'type' => 'contents',
            'contents' => $ret,
        );
    }

    public function warmupCaches()
    {
        foreach ($this->_componentDependencies as $dep) {
            $dep->warmupCaches();
        }
    }

    public function usesLanguage()
    {
        $ret = false;
        foreach ($this->_componentDependencies as $dep) {
            if ($dep->usesLanguage()) {
                $ret = true;
            }
        }
        return $ret;
    }

    public function getContentsPacked($language)
    {
        $hash = '';
        foreach ($this->_componentDependencies as $dep) {
            $src = $dep->getContentsSource();
            if ($src['type'] == 'file') {
                $hash .= md5_file($src['file']);
            } else if ($src['type'] == 'contents') {
                $hash .= md5($src['contents']);
            } else {
                throw new Kwf_Exception_NotYetImplemented();
            }
        }
        $hash = md5($hash);
        $cacheFile = "cache/componentassets/{$this->_componentClass}".
            ($this->usesLanguage() ? "-$language" : '').
            "-".Kwf_Config::getValue('application.uniquePrefix')."-$hash";

        if (file_exists($cacheFile)) {
            $ret = Kwf_SourceMaps_SourceMap::createFromInline(file_get_contents($cacheFile));
        } else {
            $ret = Kwf_SourceMaps_SourceMap::createEmptyMap('');
            foreach ($this->_componentDependencies as $dep) {
                $c = $dep->getContentsPacked($language);
                if (Kwf_Config::getValue('application.uniquePrefix')) {
                    $c->stringReplace('kwcBem--', $this->_getKwcClass().'--');
                    $c->stringReplace('kwcBem__', $this->_getKwcClass().'__');
                } else {
                    $c->stringReplace('kwcBem--', '');
                    $c->stringReplace('kwcBem__', '');
                }
                $c->stringReplace('.kwcClass', '.'.$this->_getKwcClass());
                $ret->concat($c);
            }
            file_put_contents($cacheFile, $ret->getFileContentsInlineMap());
        }

        return $ret;
    }
}
