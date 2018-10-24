<?php
class Kwf_Media_MtimeCache
{
    /**
     * @var Kwf_Media_MtimeCache
     */
    private static $_instance;


    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function setInstance($instance)
    {
        self::$_instance = $instance;
    }

    private static function _buildCacheId($class, $id, $type)
    {
        return 'mediamtime-'.preg_replace('#[^a-zA-Z0-9_]#', '_', $class.'_'.$id.'_'.$type);
    }

    public function clean()
    {
        $s = new Kwf_Model_Select();

        $rows = Kwf_Model_Abstract::getInstance('Kwf_Media_MtimeModel')->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, array('columns' => array('class', 'id', 'type')));
        foreach ($rows as $row) {
            $cacheId = self::_buildCacheId($row['class'], $row['id'], $row['type']);
            Kwf_Cache_Simple::delete($cacheId);
        }

        Kwf_Model_Abstract::getInstance('Kwf_Media_MtimeModel')->deleteRows($s);
    }

    //strange name, but matches OutputCache::clear
    public function clear($class)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('class', $class);

        $rows = Kwf_Model_Abstract::getInstance('Kwf_Media_MtimeModel')->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, array('columns' => array('class', 'id', 'type')));
        foreach ($rows as $row) {
            $cacheId = self::_buildCacheId($row['class'], $row['id'], $row['type']);
            Kwf_Cache_Simple::delete($cacheId);
        }

        Kwf_Model_Abstract::getInstance('Kwf_Media_MtimeModel')->deleteRows($s);
    }

    private function _loadOrCreateFromModel($class, $id, $type)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('class', $class);
        $s->whereEquals('id', $id);
        $s->whereEquals('type', $type);
        $rows = Kwf_Model_Abstract::getInstance('Kwf_Media_MtimeModel')->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, array('columns' => array('mtime')));
        if (!$rows) {
            $time = time();
            Kwf_Model_Abstract::getInstance('Kwf_Media_MtimeModel')->import(Kwf_Model_Abstract::FORMAT_ARRAY, array(
                array('class' => $class, 'id' => $id, 'type' => $type, 'mtime' => $time)
            ), array('replace' => true));
        } else {
            $time = $rows[0]['mtime'];
        }
        return $time;
    }

    public function loadOrCreate($class, $id, $type)
    {
        $cacheId = self::_buildCacheId($class, $id, $type);
        $ret = Kwf_Cache_Simple::fetch($cacheId);
        if ($ret === false) {
            $ret = $this->_loadOrCreateFromModel($class, $id, $type);
            Kwf_Cache_Simple::add($cacheId, $ret);
        }
        return $ret;
    }

    public function remove($class, $id, $type)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('class', $class);
        $s->whereEquals('id', $id);
        $s->whereEquals('type', $type);
        Kwf_Model_Abstract::getInstance('Kwf_Media_MtimeModel')->deleteRows($s);

        $cacheId = self::_buildCacheId($class, $id, $type);
        return Kwf_Cache_Simple::delete($cacheId);
    }
}
