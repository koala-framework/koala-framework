<?php
class Kwf_Cache_Core extends Zend_Cache_Core
{
    protected $_checkComponentSettings = true;
    public function __construct(array $options = array())
    {
        if (isset($options['checkComponentSettings'])) {
            $this->_checkComponentSettings = $options['checkComponentSettings'];
            unset($options['checkComponentSettings']);
        }
        parent::__construct($options);
    }

    public function load($cacheId, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        $ret = parent::load($cacheId, $doNotTestCacheValidity, $doNotUnserialize);

        if ($ret && isset($ret['mtimeFilesCheckAlways']) && $ret['mtimeFilesCheckAlways']) {
            foreach ($ret['mtimeFilesCheckAlways'] as $f) {
                if (file_exists($f) && filemtime($f) > $ret['mtime']) {
                    $ret = false;
                    break;
                }
            }
        }
        return $ret;
    }

    public function save(&$cacheData, $cacheId = null, $tags = array(), $specificLifetime = false, $priority = 8)
    {
        $mtime = 0;
        if (isset($cacheData['mtimeFiles'])) {
            foreach ($cacheData['mtimeFiles'] as $f) {
                if (file_exists($f)) {
                    $mtime = max($mtime, filemtime($f));
                }
            }
        }
        if ($this->_checkComponentSettings) {
            $mtime = max($mtime, Kwc_Abstract::getSettingMtime());
        }
        $mtime = max($mtime, Kwf_Registry::get('configMtime'));
        if (!isset($cacheData['mtime'])) $cacheData['mtime'] = 0;
        $cacheData['mtime'] = max($mtime, $cacheData['mtime']);
        return parent::save($cacheData, $cacheId, $tags, $specificLifetime, $priority);
    }
}
