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
                if (!file_exists($i['file']) || filemtime($i['file']) != $i['mtime']) {
                    $useCache = false;
                    break;
                }
            }
        }
        if (!$useCache) {
            $fileName = $this->getAbsoluteFileName();
            $sassc = Kwf_Config::getValue('server.sassc');
            $loadPath = array(
                Kwf_Config::getValue('path.compassMixins').'/lib',
                Kwf_Config::getValue('path.susy').'/sass',
                './scss',
                KWF_PATH.'/sass/Kwf/stylesheets',
            );

            $loadPath = escapeshellarg(implode(PATH_SEPARATOR, $loadPath));
            if (substr($fileName, 0, 1) == '.') $fileName = getcwd().substr($fileName, 1);
            $cmd = "$sassc --load-path $loadPath --style compressed ";
            $cmd .= "--sourcemap ";
            $cmd .= "$fileName ".escapeshellarg($cacheFile);
            $cmd .= " 2>&1";
            $out = array();
            exec($cmd, $out, $retVal);
            if ($retVal) {
                throw new Kwf_Exception("sassc failed: ".implode("\n", $out));
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
                    throw new Kwf_Exception("Can't find path for '$i'");
                }
                $map->sources[$k] = $f;
                $sourceFiles[] = $f;
            }
            $map->file = $cacheFile;
            file_put_contents("$cacheFile.map", json_encode($map));

            $ret = file_get_contents($cacheFile);
            $ret = preg_replace("#/\*\# sourceMappingURL=.* \*/#", '', $ret);

            $map = new Kwf_Assets_Util_SourceMap(file_get_contents("{$cacheFile}.map"), $ret);

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

            if ($baseUrl = Kwf_Setup::getBaseUrl()) {
                //TODO properly implement this, we can't do it here as it's dependent on config
                //$ret = preg_replace('#url\\((\s*[\'"]?)/assets/#', 'url($1'.$baseUrl.'/assets/', $ret);
            }

            $map->save("{$cacheFile}.map", $cacheFile);
            unset($map);

            $sourceTimes = array();
            foreach ($sourceFiles as $f) {
                $f = new Kwf_Assets_Dependency_File($f);
                $sourceTimes[] = array(
                    'file' => $f->getAbsoluteFileName(),
                    'mtime' => filemtime($f->getAbsoluteFileName())
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
