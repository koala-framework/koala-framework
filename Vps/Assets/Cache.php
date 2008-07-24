<?php
class Vps_Assets_Cache extends Zend_Cache_Core
{
    protected $_checkComponentSettings = true;
    public function __construct(array $options = array())
    {
        parent::__construct(array(
            'lifetime' => null,
            'automatic_serialization' => true
        ));
        $backend = new Zend_Cache_Backend_File(array(
            'cache_dir' => 'application/cache/assets'
        ));
        $this->setBackend($backend);

        if (isset($options['checkComponentSettings'])) {
            $this->_checkComponentSettings = $options['checkComponentSettings'];
        }
    }

    public function load($cacheId)
    {
        $ret = parent::load($cacheId);
        if ($ret && Vps_Registry::get('config')->debug->componentCache->checkComponentModification)
        {
            if (isset($ret['mtimeFiles'])) {
                foreach ($ret['mtimeFiles'] as $f) {
                    if (file_exists($f) && filemtime($f) > $ret['mtime']) {
                        $ret = false;
                        break;
                    }
                }
            }
            if ($ret && $this->_checkComponentSettings)
            {
                if ($ret['mtime'] < Vpc_Abstract::getSettingMtime()) {
                    $ret = false;
                }
            }
            if ($ret && $ret['mtime'] < Vps_Registry::get('configMtime')) {
                $ret = false;
            }
        }
        return $ret;
    }

    public function save(&$cacheData, $cacheId)
    {
        if (!isset($cacheData['mtime'])) $cacheData['mtime'] = 0;
        if (isset($cacheData['mtimeFiles'])) {
            foreach ($cacheData['mtimeFiles'] as $f) {
                if (file_exists($f)) {
                    $cacheData['mtime'] = max($cacheData['mtime'], filemtime($f));
                }
            }
        }
        $cacheData['mtime'] = max($cacheData['mtime'], Vpc_Abstract::getSettingMtime());
        $cacheData['mtime'] = max($cacheData['mtime'], Vps_Registry::get('configMtime'));
        return parent::save($cacheData, $cacheId);
    }
}
