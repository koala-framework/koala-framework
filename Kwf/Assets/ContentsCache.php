<?php
class Kwf_Assets_ContentsCache
{
    private $_filemd5Cache = array();
    private $_cache = array();

    public static function getInstance()
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new self();
        }
        return $cache;
    }

    public function save(Kwf_SourceMaps_SourceMap $map, $cacheId, Kwf_Assets_ProviderList_Abstract $providerList)
    {
        $this->_cache[$cacheId] = $map;

        $cacheFile = 'cache/assetdeps/'.md5($cacheId);

        $map->getMapContentsData(true); //this will trigger _generateMappings
        file_put_contents($cacheFile, serialize($map));

        $masterFiles = array();
        foreach ($map->getSources() as $f) {
            if (!file_exists($f)) {
                $f = new Kwf_Assets_Dependency_File($providerList, $f);//TODO providerList
                $f = $f->getAbsoluteFileName();
            }
            $masterFiles[] = array(
                'file' => $f,
                'md5' => file_exists($f) ? md5_file($f) : false
            );
        }
        file_put_contents($cacheFile.'.masterFiles', serialize($masterFiles));
    }

    public function load($cacheId)
    {
        if (isset($this->_cache[$cacheId])) {
            return $this->_cache[$cacheId];
        }
        $cacheFile = 'cache/assetdeps/'.md5($cacheId);
        if (!file_exists($cacheFile) || !file_exists($cacheFile.'.masterFiles')) {
            return false;
        } else {
            $masterFiles = unserialize(file_get_contents($cacheFile.'.masterFiles'));
            $mtime = filemtime($cacheFile);
            foreach ($masterFiles as $i) {
                if (!isset($this->_filemd5Cache[$i['file']])) {
                    if (!file_exists($i['file'])) {
                        $this->_filemd5Cache[$i['file']] = false;
                    } else {
                        $this->_filemd5Cache[$i['file']] = md5_file($i['file']);
                    }
                }
                if ($i['md5'] != $this->_filemd5Cache[$i['file']]) {
                    return false;
                }
            }
        }

        $ret = unserialize(file_get_contents($cacheFile));
        $this->_cache[$cacheId] = $ret;
        return $ret;
    }
}
