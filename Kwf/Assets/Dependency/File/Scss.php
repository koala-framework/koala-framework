<?php
class Kwf_Assets_Dependency_File_Scss extends Kwf_Assets_Dependency_File_Css
{
    private function _getCacheFileName()
    {
        $fileName = $this->getFileNameWithType();
        return 'cache/scss/v4'.str_replace(array('\\', ':', '/', '.', '-'), '_', $fileName);
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

    public function warmupCaches()
    {
        $cacheFile = $this->_getCacheFileName();
        $useCache = false;
        if (file_exists("$cacheFile.sourcetimes")) {
            $useCache = true;
            $sourceTimes = unserialize(file_get_contents("$cacheFile.sourcetimes"));
            foreach ($sourceTimes as $i) {
                if ($i['mtime']) {
                    if (!file_exists($i['file']) || filemtime($i['file']) != $i['mtime']) {
                        //file was modified or deleted
                        $useCache = false;
                        break;
                    }
                } else {
                    if (file_exists($i['file'])) {
                        //file didn't exist, was created
                        $useCache = false;
                        break;
                    }
                }
            }
        }
        if (!$useCache) {
            $fileName = $this->getAbsoluteFileName();
            static $loadPath;
            if (!isset($loadPath)) {
                $loadPath = array();
                foreach (glob(VENDOR_PATH.'/bower_components/*') as $p) {
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
                $loadPath = escapeshellarg(implode(PATH_SEPARATOR, $loadPath));
            }

            if (substr($fileName, 0, 2) == './') $fileName = getcwd().substr($fileName, 1);
            $bin = Kwf_Config::getValue('server.nodeSassBinary');
            if (!$bin) {
                $bin = getcwd()."/".VENDOR_PATH."/bin/node ".dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/node_modules/node-sass/bin/node-sass';
            }
            $cmd = "$bin --include-path $loadPath --output-style compressed ";
            $cmd .= " --source-map ".escapeshellarg($cacheFile.'.map');
            $cmd .= " ".escapeshellarg($fileName)." ".escapeshellarg($cacheFile);
            $cmd .= " 2>&1";
            $out = array();
            exec($cmd, $out, $retVal);
            if ($retVal) {
                throw new Kwf_Exception("compiling sass failed: ".implode("\n", $out));
            }
            $map = json_decode(file_get_contents("{$cacheFile}.map"));
            $sourceFiles = array();
            foreach ($map->sources as $k=>$i) {
                //sources are relative to cache/sass, strip that
                if (substr($i, 0, 6) != '../../') {
                    throw new Kwf_Exception('source doesn\'t start with ../../');
                }
                $i = substr($i, 6);
                $f = self::getPathWithTypeByFileName(getcwd().'/'.$i);
                if (!$f) {
                    throw new Kwf_Exception("Can't find path for '".getcwd().'/'.$i."'");
                }
                $map->sources[$k] = $f;
                $sourceFiles[] = $f;
                $sourceFiles[] = 'config.ini'; //for uniquePrefix
                if (substr($f, 0, 16) == 'web/scss/config/') {
                    $sourceFiles[] = 'kwf/sass/Kwf/stylesheets/config/'.substr($f, 16);
                } else if (substr($f, 0, 32) == 'kwf/sass/Kwf/stylesheets/config/') {
                    $sourceFiles[] = 'web/scss/config/'.substr($f, 32);
                }
            }

            $map->file = $cacheFile;
            file_put_contents("$cacheFile.map", json_encode($map));

            $ret = file_get_contents($cacheFile);
            $ret = str_replace("@charset \"UTF-8\";\n", '', $ret); //remove charset, no need to adjust sourcemap as sourcemap doesn't include that (bug in libsass)
            $ret = preg_replace("#/\*\# sourceMappingURL=.* \*/#", '', $ret);

            $map = new Kwf_SourceMaps_SourceMap(file_get_contents("{$cacheFile}.map"), $ret);

            if (strpos($ret, 'kwfUp-') !== false) {
                if (Kwf_Config::getValue('application.uniquePrefix')) {
                    $map->stringReplace('kwfUp-', Kwf_Config::getValue('application.uniquePrefix').'-');
                } else {
                    $map->stringReplace('kwfUp-', '');
                }
            }

            $map->save("{$cacheFile}.map", $cacheFile);
            unset($map);

            $sourceTimes = array();
            foreach ($sourceFiles as $f) {
                $f = new Kwf_Assets_Dependency_File($f);
                $f = $f->getAbsoluteFileName();
                $sourceTimes[] = array(
                    'file' => $f,
                    'mtime' => file_exists($f) ? filemtime($f) : null
                );
            }

            file_put_contents("$cacheFile.sourcetimes", serialize($sourceTimes));
        }
    }

    public function getContents($language)
    {
        $cacheFile = $this->_getCacheFileName();
        if (!file_exists("$cacheFile.buildtime") || filemtime($this->getAbsoluteFileName()) != file_get_contents("$cacheFile.buildtime")) {
            $this->warmupCaches();
        }
        $ret = file_get_contents($cacheFile);
        return $ret;
    }

    public function getContentsPacked($language)
    {
        $cacheFile = $this->_getCacheFileName();
        if (!file_exists("$cacheFile.buildtime") || filemtime($this->getAbsoluteFileName()) != file_get_contents("$cacheFile.buildtime")) {
            $this->warmupCaches();
        }
        return new Kwf_SourceMaps_SourceMap(file_get_contents($cacheFile.'.map'), file_get_contents($cacheFile));
    }
}
