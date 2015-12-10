<?php
class Kwf_Assets_ContentsCache
{
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
        $cacheFile = 'cache/assetdeps/'.md5($cacheId);
        file_put_contents($cacheFile, $map->getFileContentsInlineMap());

        $masterFiles = array();
        foreach ($map->getSources() as $f) {
            if (!file_exists($f)) {
                $f = new Kwf_Assets_Dependency_File($f);
                $f = $f->getAbsoluteFileName();
            }
            $masterFiles[] = array(
                'file' => $f,
                'md5' => file_exists($f) ? md5_file($f) : null
            );
        }
        file_put_contents($cacheFile.'.masterFiles', json_encode($masterFiles));
    }

    public function load($cacheId)
    {
        $cacheFile = 'cache/assetdeps/'.md5($cacheId);
        if (!file_exists($cacheFile) || !file_exists($cacheFile.'.masterFiles')) {
            return false;
        } else {
            $masterFiles = json_decode(file_get_contents($cacheFile.'.masterFiles'), true);
            $mtime = filemtime($cacheFile);
            foreach ($masterFiles as $i) {
                if ($i['md5']) {
                    if (!file_exists($i['file']) || md5_file($i['file']) != $i['md5']) {
                        //file was modified or deleted
                        return false;
                    }
                } else {
                    if (file_exists($i['file'])) {
                        //file didn't exist, was created
                        return false;
                    }
                }
            }
        }

        return Kwf_SourceMaps_SourceMap::createFromInline(file_get_contents($cacheFile));
    }
}
