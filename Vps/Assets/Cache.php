<?php
class Vps_Assets_Cache extends Zend_Cache_Core
{
    public function __construct()
    {
        parent::__construct(array(
            'lifetime' => null,
            'automatic_serialization' => true
        ));
        $backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => 'application/cache/assets'
        ));
        $this->setBackend($backend);
    }

    public function load($cacheId)
    {
        $ret = parent::load($cacheId);
        if ($ret && isset($ret['mtimeFiles'])
            && Vps_Registry::get('config')->debug->componentCache->checkComponentModification)
        {
            $mtime = 0;
            foreach ($ret['mtimeFiles'] as $f) {
                if (filemtime($f) > $ret['mtime']) {
                    $ret = false;
                    break;
                }
            }
        }
        return $ret;
    }

    public function save(&$cacheData, $cacheId)
    {
        if (isset($cacheData['mtimeFiles'])) {
            if (!isset($cacheData['mtime'])) $cacheData['mtime'] = 0;
            foreach ($cacheData['mtimeFiles'] as $f) {
                $cacheData['mtime'] = max($cacheData['mtime'], filemtime($f));
            }
        }
        return parent::save($cacheData, $cacheId);
    }
}
