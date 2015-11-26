<?php
class Kwf_Assets_Dependency_File_Scss extends Kwf_Assets_Dependency_File_Css
{
    private $_cacheWarm = false;
    private $_config = null;
    private function _getCacheFileName()
    {
        $fileName = $this->getFileNameWithType();
        return 'cache/scss/v5'.str_replace(array('\\', ':', '/', '.', '-'), '_', $fileName);
    }

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

    public function getContentsPacked($language)
    {
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
            $loadPath[] = 'cache/scss/generated';
            $loadPath = escapeshellarg(implode(PATH_SEPARATOR, $loadPath));
        }

        if (substr($fileName, 0, 2) == './') $fileName = getcwd().substr($fileName, 1);

        $wrapperContents = "";
        $wrapperContents .= "@import \"config/global-settings\";\n";
        $wrapperContents .= "@import \"$fileName\";\n";
        $wrapperFile = tempnam('temp', 'scsswrapper');
        file_put_contents($wrapperFile, $wrapperContents);

        $bin = Kwf_Config::getValue('server.nodeSassBinary');
        if (!$bin) {
            $bin = getcwd()."/".VENDOR_PATH."/bin/node ".dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/node_modules/node-sass/bin/node-sass';
        } else {
            $p = json_decode(file_get_contents(KWF_PATH.'/node_modules/node-sass/package.json'), true);
            $bin = str_replace('%version%', $p['version'], $bin);
            unset($p);
        }
        $cmd = "$bin --include-path $loadPath --output-style compressed ";
        $cmd .= " --source-map ".escapeshellarg($cacheFile.'.map');
        $cmd .= " ".escapeshellarg($wrapperFile)." ".escapeshellarg($cacheFile);
        $cmd .= " 2>&1";
        $out = array();
        exec($cmd, $out, $retVal);
        if ($retVal) {
            throw new Kwf_Exception("compiling sass failed: ".implode("\n", $out));
        }
        unlink($wrapperFile);
        if ($this->_config) {
            unlink('cache/scss/generated/_config.scss');
        }
        $map = json_decode(file_get_contents("{$cacheFile}.map"));
        $sources = array();
        $additionalSources = array();
        foreach ($map->sources as $k=>$i) {
            //sources are relative to cache/sass, strip that
            if (substr($i, 0, 10) == 'generated/') {
                $f = 'web/cache/scss/'.$i;
            } else  {
                if (substr($i, 0, 6) != '../../') {
                    throw new Kwf_Exception('source doesn\'t start with ../../');
                }
                $i = substr($i, 6);
                $f = self::getPathWithTypeByFileName(getcwd().'/'.$i);
                if (!$f) {
                    throw new Kwf_Exception("Can't find path for '".getcwd().'/'.$i."'");
                }
            }
            $sources[$k] = $f;
            if (substr($f, 0, 16) == 'web/scss/config/') {
                $additionalSources[] = 'kwf/sass/Kwf/stylesheets/config/'.substr($f, 16);
            } else if (substr($f, 0, 32) == 'kwf/sass/Kwf/stylesheets/config/') {
                $additionalSources[] = 'web/scss/config/'.substr($f, 32);
            }
        }
        $sources[] = 'config.ini'; //for uniquePrefix
        $map->sources = array_merge($sources, $additionalSources);

        $map->file = $cacheFile;
        file_put_contents("$cacheFile.map", json_encode($map));

        $ret = file_get_contents($cacheFile);
        $ret = str_replace("@charset \"UTF-8\";\n", '', $ret); //remove charset, no need to adjust sourcemap as sourcemap doesn't include that (bug in libsass)
        $ret = str_replace(chr(0xEF).chr(0xBB).chr(0xBF), '', $ret); //remove byte order mark
        $ret = preg_replace("#/\*\# sourceMappingURL=.* \*/#", '', $ret);

        $map = new Kwf_SourceMaps_SourceMap(file_get_contents("{$cacheFile}.map"), $ret);
        $map->setMimeType('text/css');

        if (strpos($ret, 'kwfUp-') !== false) {
            if (Kwf_Config::getValue('application.uniquePrefix')) {
                $map->stringReplace('kwfUp-', Kwf_Config::getValue('application.uniquePrefix').'-');
            } else {
                $map->stringReplace('kwfUp-', '');
            }
        }

        return $map;
    }

    public function setConfig(array $config)
    {
        $this->_config = $config;
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
