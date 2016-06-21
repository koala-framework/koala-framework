<?php
class Kwf_Assets_Dependency_File_Scss extends Kwf_Assets_Dependency_File_Css
{
    private $_config = null;
    private $_configMasterFiles = array();

    private static function _getAbsolutePath($path)
    {
        if (substr($path, 0, 1)=='.') $path = getcwd().'/'.$path;
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    public function getContentsPacked()
    {
        $cacheId = 'scss-v2-'.$this->getIdentifier();
        $ret = Kwf_Assets_ContentsCache::getInstance()->load($cacheId);
        if ($ret !== false) {
            return $ret;
        }

        $fileName = $this->getAbsoluteFileName();
        static $loadPath;
        if (!isset($loadPath)) {
            $loadPath = array();
            foreach (glob(realpath(VENDOR_PATH).'/*/*') as $p) {
                $bowerMain = null;
                $mainExt = null;
                if (file_exists($p.'/bower.json')) {
                    $bower = json_decode(file_get_contents($p.'/bower.json'));
                    if (isset($bower->main) && is_string($bower->main)) {
                        $bowerMain = $bower->main;
                        $mainExt = substr($bowerMain, -5);
                    }
                }
                if ($mainExt == '.scss' || $mainExt == '.sass') {
                    $mainDir = substr($bowerMain, 0, strrpos($bowerMain, '/'));
                    $loadPath[] = $p.'/'.$mainDir;
                } else if (file_exists($p.'/scss')) {
                    $loadPath[] = $p.'/scss';
                }
            }
            $loadPath[] = './scss';
            if (KWF_PATH == '..') {
                $loadPath[] = substr(getcwd(), 0, strrpos(getcwd(), '/')).'/sass/Kwf/stylesheets';
            } else {
                $loadPath[] = KWF_PATH.'/sass/Kwf/stylesheets';
            }
            $loadPath[] = 'temp/scss-generated';
            $loadPath = escapeshellarg(implode(PATH_SEPARATOR, $loadPath));
        }

        $buildFile = 'temp/scss-'.str_replace(array('\\', ':', '/', '.', '-'), '_', $this->getFileNameWithType());

        if (!is_dir('temp/scss-generated')) mkdir('temp/scss-generated');
        if ($this->_config) {
            $config = "\$config: ".self::_generateScssConfig($this->_config).";\n";
            file_put_contents('temp/scss-generated/_config.scss', $config);
        } else {
            if (file_exists('temp/scss-generated/_config.scss')) {
                unlink('temp/scss-generated/_config.scss');
            }
        }

        if (substr($fileName, 0, 2) == './') $fileName = str_replace(DIRECTORY_SEPARATOR, '/', getcwd()).substr($fileName, 1);

        $wrapperContents = "";
        $wrapperContents .= "@import \"config/global-settings\";\n";
        $wrapperContents .= "@import \"$fileName\";\n";
        $wrapperFile = tempnam('temp', 'scw');
        file_put_contents($wrapperFile, $wrapperContents);
        chmod($wrapperFile, 0777);

        $bin = Kwf_Config::getValue('server.nodeSassBinary');
        if (!$bin) {
            $bin = getcwd()."/".VENDOR_PATH."/bin/node ".dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/node_modules/node-sass/bin/node-sass';
        } else {
            $p = json_decode(file_get_contents(KWF_PATH.'/node_modules/node-sass/package.json'), true);
            $bin = str_replace('%version%', $p['version'], $bin);
            unset($p);
        }
        $cmd = "$bin --include-path $loadPath --output-style compressed ";
        $cmd .= " --source-map ".escapeshellarg($buildFile.'.map');
        $cmd .= " ".escapeshellarg($wrapperFile)." ".escapeshellarg($buildFile);
        $cmd .= " 2>&1";
        $out = array();
        exec($cmd, $out, $retVal);
        if ($retVal) {
            throw new Kwf_Exception("compiling sass failed: ".implode("\n", $out));
        }
        $map = json_decode(file_get_contents("{$buildFile}.map"));
        $sources = array();
        $masterFiles = $this->_configMasterFiles;
        foreach ($map->sources as $k=>$i) {
            //sources are relative to cache/sass, strip that
            if (substr($i, 0, 15) == 'scss-generated/' || substr($i, 0, 3) == 'scw') {
                $f = substr($this->getFileNameWithType(), 0, -5).'/temp/'.$i;
                $map->{'_x_org_koala-framework_sourcesContent'}[$k] = file_get_contents('temp/'.$i);
                $masterFiles[] = 'temp/'.$i;
            } else  {
                if (substr($i, 0, 3) != '../') {
                    throw new Kwf_Exception('source doesn\'t start with ../: '.$i);
                }
                $i = substr($i, 3);
                $f = self::getPathWithTypeByFileName($this->_providerList, getcwd().'/'.$i);
                if (!$f) {
                    throw new Kwf_Exception("Can't find path for '".getcwd().'/'.$i."'");
                }
                $dep = new Kwf_Assets_Dependency_File($this->_providerList, $i);
                $masterFiles[] = $dep->getAbsoluteFileName();
            }
            $sources[$k] = '/assets/'.$f;
            if (substr($f, 0, 16) == 'web/scss/config/') {
                $masterFiles[] = KWF_PATH.'/sass/Kwf/stylesheets/config/'.substr($f, 16);
            } else if (substr($f, 0, 32) == 'kwf/sass/Kwf/stylesheets/config/') {
                $masterFiles[] = 'scss/config/'.substr($f, 32);
            }
        }
        $masterFiles[] = 'config.ini'; //for uniquePrefix

        $map->{'_x_org_koala-framework_masterFiles'} = $masterFiles;

        $map->sources = $sources;
        $map->file = $buildFile;
        file_put_contents("$buildFile.map", json_encode($map));

        unlink($wrapperFile);
        if ($this->_config) {
            unlink('temp/scss-generated/_config.scss');
        }

        $ret = file_get_contents($buildFile);
        $ret = str_replace("@charset \"UTF-8\";\n", '', $ret); //remove charset, no need to adjust sourcemap as sourcemap doesn't include that (bug in libsass)
        $ret = str_replace(chr(0xEF).chr(0xBB).chr(0xBF), '', $ret); //remove byte order mark
        $ret = preg_replace("#/\*\# sourceMappingURL=.* \*/#", '', $ret);

        $map = new Kwf_SourceMaps_SourceMap(file_get_contents("{$buildFile}.map"), $ret);
        $map->setMimeType('text/css');

        unlink($buildFile);
        unlink("{$buildFile}.map");

        Kwf_Assets_ContentsCache::getInstance()->save($map, $cacheId);

        return $map;
    }

    public function setConfig(array $config, array $masterFiles = array())
    {
        $this->_config = $config;
        $this->_configMasterFiles = $masterFiles;
    }

    private static function _generateScssConfig($config)
    {
        if (is_array($config)) {
            $ret = '(';
            $keys = array_keys($config);
            $isList = $keys && $keys[0] === 0;
            foreach ($config as $k=>$i) {
                if (!$isList) {
                    $ret .= "$k:";
                }
                $ret .= self::_generateScssConfig($i);
                $ret .= ',';
            }
            $ret = substr($ret, 0, -1);
            $ret .= ')';
            return $ret;
        } else if (is_bool($config)) {
            return $config ? 'true' : 'false';
        } else if (is_null($config)) {
            return 'null';
        } else if (is_string($config)) {
            return '"'.str_replace('"', '\\"', $config).'"';
        } else if (is_numeric($config)) {
            return $config;
        } else {
            throw new Kwf_Exception("Unsupported type");
        }
    }
}
