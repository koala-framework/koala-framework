<?php
class Vps_Dao_TreeCache extends Vps_Db_Table
{
    protected $_name = 'vps_tree_cache';
    protected $_rowClass = 'Vps_Dao_Row_TreeCache';
    protected $_primary = 'component_id';
    protected $_referenceMap    = array(
        'Parent' => array(
            'columns'           => array('parent_component_id'),
            'refTableClass'     => 'Vps_Dao_TreeCache',
            'refColumns'        => array('component_id')
        )
    );
    
    private $_decoratorTreeCaches;
    private $_dao;

    const NOT_GENERATED = 0;
    const GENERATE_START = 1;
    const GENERATE_AFTER = 2;
    const GENERATE_AFTER_START = 3;
    const GENERATE_FINISHED = 4;

    public function regenerate()
    {
        ini_set('memory_limit', '64M');
//         $this->getAdapter()->beginTransaction();
        $this->getAdapter()->query("TRUNCATE TABLE $this->_name");
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            $tc = Vpc_TreeCache_Abstract::getInstance($class);
            if ($tc) $tc->createRoot();
        }
        $this->createMissingChilds();
//         $this->getAdapter()->commit();
    }
    
    private function _getDecoratorTreeCaches()
    {
        if (!$this->_decoratorTreeCaches) {
            $this->_decoratorTreeCaches = array();
            foreach (Zend_Registry::get('config')->vpc->pageDecorators->toArray() as $decorator) {
                $tc = Vpc_TreeCache_Abstract::getInstance($decorator);
                if ($tc) { $this->_decoratorTreeCaches[] = $tc; }
            }
        }
        return $this->_decoratorTreeCaches;
    }

    public function createMissingChilds()
    {
        do {
            $s = new Zend_Db_Select($this->getAdapter());
            $s->from('vps_tree_cache', array('component_class',
                                    'count'=>'COUNT(component_id)',
                                    'hasurl'=>'MAX(NOT ISNULL(url))'));
            $s->where('generated=?', self::NOT_GENERATED);
            $s->group('component_class');
            $rows = $s->query()->fetchAll();
            foreach ($rows as $row) {
                $tc = Vpc_TreeCache_Abstract::getInstance($row['component_class']);
                if (Zend_Registry::isRegistered('debugLogger')) {
                    $logger = Zend_Registry::get('debugLogger');
                    $logger->info($row['component_class']);
                    if (!$tc) {
                        $logger->debug('(kein TreeCache existiert)');
                    }
                }
                $generated = self::GENERATE_FINISHED;
                $where = array(
                    'component_class = ?' => $row['component_class'],
                );
                if ($tc) {
                    $where['generated = ?'] = self::NOT_GENERATED;
                    $this->updateGenerated(self::GENERATE_START, $where);
                    $tc->createMissingChilds();
                    if ($tc instanceof Vpc_TreeCache_AfterGenerate_Interface) {
                        $generated = self::GENERATE_AFTER;
                    }
                    $where['generated = ?'] = self::GENERATE_START;
                } else {
                    $where['generated = ?'] = self::NOT_GENERATED;
                }
                if ($row['hasurl']) {
                    foreach ($this->_getDecoratorTreeCaches() as $tc) {
                        $tc->createMissingChilds($row['component_class']);
                    }
                }
                $this->updateGenerated($generated, $where);
            }
        } while(count($rows));

        $this->afterGenerate();
    }

    public function updateGenerated($generated, $where)
    {
        $w = array();
        foreach ($where as $k=>$i) {
            if (!is_int($k)) {
                $i = $this->getAdapter()->quoteInto($k, $i);
            }
            $w[] = $i;
        }
        if (Zend_Registry::isRegistered('debugLogger')) {
            $logger = Zend_Registry::get('debugLogger');
            $logger->debug("set generated to $generated (".implode(' AND ', $w).")");
        }

        return $this->update(array('generated'=>$generated), $w);
    }

    public function afterGenerate()
    {
        // Mehrfachboxen löschen
        $sql = "SELECT parent_url, box, (COUNT(*) - 1) c FROM vps_tree_cache
            WHERE NOT ISNULL(box)
            GROUP BY parent_url, box
            HAVING c > 0";
        foreach ($this->getAdapter()->fetchAll($sql) as $row) {
            $sql = "DELETE FROM vps_tree_cache 
                WHERE parent_url=:parent_url AND box=:box 
                ORDER BY box_priority ASC LIMIT :c";
            $this->getAdapter()->query($sql, $row);
        }

        do {
            $select = $this->getAdapter()->select();
            $select->from($this->_name, array('component_class'))
                ->group('component_class')
                ->where('generated = ?', self::GENERATE_AFTER);
            $rows = $select->query()->fetchAll();
            foreach ($rows as $row) {
                $tc = Vpc_TreeCache_Abstract::getInstance($row['component_class']);
                if ($tc) $tc->afterGenerate();
            }
        } while(count($rows));
    }

    public function setDao($dao)
    {
        $this->_dao = $dao;
    }

    public function getDao()
    {
        return $this->_dao;
    }
    public function showInvisible()
    {
        return Zend_Registry::get('config')->showInvisible;
    }


    public function findByDbId($id)
    {
        return $this->fetchAll(array('db_id = ?'=>$id));
    }

    public function findPage($id)
    {
        $where = array('component_id = ?'=>$id);
        $where[] = 'NOT ISNULL(url)';
        return $this->fetchAll($where)->current();
    }

    public function findPageByPath($path)
    {
        $where = array('tree_url = ?'=>$path);
        return $this->fetchAll($where)->current();
    }

    public function findComponentByClass($class)
    {
        $where = array('component_class = ?'=>$class);
        return $this->fetchAll($where, null, 1)->current();
    }
    public function findComponentsByClass($class)
    {
        $where = array('component_class = ?'=>$class);
        return $this->fetchAll($where);
    }

    public function findComponentByParentClass($parentClass)
    {
        $matchingClasses = array();
        $classes = Vpc_Abstract::getComponentClasses();
        foreach ($classes as $class) {
            if (is_subclass_of($class, $parentClass) || $class == $parentClass) {
                $matchingClasses[] = $class;
            }
        }

        foreach ($matchingClasses as $class) {
            $ret = $this->getComponentByClass($class);
            if ($ret) return $ret;
        }
        return null;
    }
}
