<?php
class Kwf_Assets_Dependency_File_Scss extends Kwf_Assets_Dependency_File_Css
{
    private function _getCacheFileName()
    {
        $fileName = $this->getFileNameWithType();
        return 'cache/scss/v2'.str_replace(array('\\', ':', '/', '.', '-'), '_', $fileName);
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
            $loadPath = array(
                VENDOR_PATH.'/bower_components/compass-mixins/lib',
                VENDOR_PATH.'/bower_components/susy/sass',
                './scss',
                KWF_PATH.'/sass/Kwf/stylesheets',
            );

            $loadPath = escapeshellarg(implode(PATH_SEPARATOR, $loadPath));
            if (substr($fileName, 0, 1) == '.') $fileName = getcwd().substr($fileName, 1);
            $bin = Kwf_Config::getValue('server.nodeSassBinary');
            if (!$bin) {
                $bin = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/node_modules/.bin/node-sass';
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
                if (substr($f, 0, 16) == 'web/scss/config/') {
                    $sourceFiles[] = 'kwf/sass/Kwf/stylesheets/config/'.substr($f, 16);
                } else if (substr($f, 0, 32) == 'kwf/sass/Kwf/stylesheets/config/') {
                    $sourceFiles[] = 'web/scss/config/'.substr($f, 32);
                }
            }

            $map->file = $cacheFile;
            file_put_contents("$cacheFile.map", json_encode($map));

            $ret = file_get_contents($cacheFile);
            $ret = preg_replace("#/\*\# sourceMappingURL=.* \*/#", '', $ret);

            $map = new Kwf_SourceMaps_SourceMap(file_get_contents("{$cacheFile}.map"), $ret);

            if (strpos($ret, 'cssClass') !== false && (strpos($ret, '$cssClass') !== false || strpos($ret, '.cssClass') !== false)) {
                $cssClass = $this->_getComponentCssClass();
                if ($cssClass) {
                    if (strpos($ret, '.cssClass') !== false) {
                        $map->stringReplace('.cssClass', ".$cssClass");
                    }
                    if (strpos($ret, '$cssClass') !== false) {
                        $map->stringReplace('$cssClass', ".$cssClass");
                    }
                }
            }
            $assetVars = self::getAssetVariables();
            foreach ($assetVars as $k=>$i) {
                $search = 'var('.$k.')';
                if (strpos($ret, $search) !== false) {
                    $map->stringReplace($search, $i);
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
        return $this->getContents($language);
    }

    public function getContentsPackedSourceMap($language)
    {
        $this->getContents($language);
        $cacheFile = $this->_getCacheFileName(); //generates map if not existing or outdated
        return file_get_contents($cacheFile.'.map');
    }
}
