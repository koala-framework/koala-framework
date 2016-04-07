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

    public function save(Kwf_SourceMaps_SourceMap $map, $cacheId)
    {
        $this->_cache[$cacheId] = $map;

        $cacheFile = 'cache/assetdeps/'.md5($cacheId).'v2';

        $data = $map->getMapContentsData(true); //this will trigger _generateMappings
        file_put_contents($cacheFile, serialize($map));

        $masterFiles = array();
        if (isset($data->{'_x_org_koala-framework_masterFiles'})) {
            foreach ($data->{'_x_org_koala-framework_masterFiles'} as $f) {
                $masterFiles[] = array(
                    'file' => $f,
                    'hash' => $this->_hashFile($f)
                );
            }
        }
        file_put_contents($cacheFile.'.masterFiles', serialize($masterFiles));
    }

    private function _hashFile($f)
    {
        if (isset($this->_filemd5Cache[$f])) {
            return $this->_filemd5Cache[$f];
        }
        $ret = null;
        if (!file_exists($f)) {
            $ret = false;
        } else if (substr($f, -13) == '/package.json') {
            //package.json changes on install even though installed version didn't change (_shasum field)
            $c = json_decode(file_get_contents($f), true);
            if (isset($c['_resolved'])) {
                $ret = $c['_resolved'];
            }
        }
        if (is_null($ret)) {
            $ret = md5_file($f);
        }
        $this->_filemd5Cache[$f] = $ret;
        return $ret;
    }

    public function load($cacheId)
    {
        if (isset($this->_cache[$cacheId])) {
            return $this->_cache[$cacheId];
        }

        $cacheFile = 'cache/assetdeps/'.md5($cacheId).'v2';
        if (!file_exists($cacheFile) || !file_exists($cacheFile.'.masterFiles')) {
            return false;
        } else {
            $masterFiles = unserialize(file_get_contents($cacheFile.'.masterFiles'));
            $mtime = filemtime($cacheFile);
            foreach ($masterFiles as $i) {
                if ($i['hash'] != $this->_hashFile($i['file'])) {
                    return false;
                }
            }
        }

        $ret = unserialize(file_get_contents($cacheFile));
        $this->_cache[$cacheId] = $ret;
        return $ret;
    }
}
