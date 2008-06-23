<?php
class Vps_Component_Data_Root extends Vps_Component_Data
{
    
    private static $_instance;
    private $_hasChildComponentCache;
private static $debugClassesCheckedCounter;
    
    public static function getInstance()
    {
        if (!self::$_instance) {
            $componentClass = Vps_Registry::get('config')->vpc->rootComponent;
            self::$_instance = new self(array(
                'componentClass' => $componentClass,
                'name' => '',
                'parent' => null,
                'isPage' => false
            ));
        }
        return self::$_instance;
    }
    
    public function getPageByPath($path)
    {
        if ($path == '/') {
            return $this->getChildPage(array('home' => true));
        } else {
            $page = $this;
            foreach (explode('/', substr($path, 1)) as $pathPart) {
                $page = $page->getChildPage(array('filename' => $pathPart));
                p('================='.$pathPart);
            }
            return $page;
        }
    }

    public function getComponentById($componentId, $page = null)
    {
$GLOBALS['getComponentByIdCalled'][] = $componentId;
        $ids = preg_split('/([_\-])/', $componentId, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!$page) $page = $this;
        for ($i = 0; $i < count($ids); $i++) {
            $idPart = $ids[$i];
            if ($i > 0) {
                $i++;
                $idPart .= $ids[$i];
            }
            $page = $page->getChildComponent($idPart);
            if (!$page) break;
        }
        return $page;
    }
    
    public function getByDbId($dbId)
    {
static $cnt = 0;
$cnt++;
// if ($cnt > 3) {
    //$bt = debug_backtrace();
    //p($bt[2]['class'].' '.$bt[2]['object']->getData()->componentId);
// }
$startQueryCount = Zend_Registry::get('db')->getProfiler()->getQueryCount();
$start = microtime(true);
        if (is_numeric(substr($dbId, 0, 1))) {
            $data = $this->getComponentById($dbId);
$queryCount = Zend_Registry::get('db')->getProfiler()->getQueryCount() - $startQueryCount;
//p('Looking for dbId '.$dbId. ' in '.(microtime(true)-$start) . ' sec; '.$queryCount.' db-queryies');
            return $data;
        }
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            $tc = $this->_getTreeCache($class);
            if ($tc && ($dbIdShortcut = $tc->getDbIdShortcut($dbId))) {
                foreach ($this->getComponentsByClass($class) as $data) {
                    $id = '-'.substr($dbId, strlen($dbIdShortcut));
                    $data = $this->getComponentById($id, $data);
                    if ($data) {
$queryCount = Zend_Registry::get('db')->getProfiler()->getQueryCount() - $startQueryCount;
//p('Looking for dbId '.$dbId. ' in '.(microtime(true)-$start) . ' sec; '.$queryCount.' db-queryies');
                        return $data;
                    }
                }
            }
        }
//p('NIX GEFUNDEN BEIM SUCHEN NACH DB_ID');
        return null;
    }
    
    public function getComponentsByClass($class)
    {
//static $cnt = 0;
//$cnt++;
//if ($cnt > 1) echo $x;
self::$debugClassesCheckedCounter = 0;
if (Zend_Registry::get('db')->getProfiler() instanceof Vps_Db_Profiler) {
    $startQueryCount = Zend_Registry::get('db')->getProfiler()->getQueryCount();
}
$start = microtime(true);
        $this->_hasChildComponentCache = array();
        $lookingForChildClasses = array();
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            if (is_subclass_of($c, $class) || $c == $class) {
                $lookingForChildClasses[] = $c;
            }
        }
        $ret = $this->_getComponentsByClassRek($lookingForChildClasses, $this);
if (Zend_Registry::get('db')->getProfiler() instanceof Vps_Db_Profiler) {
    $queryCount = Zend_Registry::get('db')->getProfiler()->getQueryCount() - $startQueryCount;
} else {
    $queryCount = '?';
}
//p('Looking for '.$class. ' in '.(microtime(true)-$start) . ' sec; '.self::$debugClassesCheckedCounter.' components checked; '.$queryCount.' db-queryies');
        return $ret;
    }

    private function _getComponentsByClassRek($lookingForChildClasses, $data)
    {
        $ret = array();
        $constraintClasses = array();
        foreach (Vpc_Abstract::getSetting($data->componentClass, 'childComponentClasses') as $c) {
            if ($this->_hasChildComponentClass($c, $lookingForChildClasses)) {
                $constraintClasses[] = $c;
            }
        }

        $constraint = array('componentClass' => $constraintClasses);
        foreach ($data->getChildComponents($constraint) as $childData) {
self::$debugClassesCheckedCounter++;
            if (in_array($childData->componentClass, $lookingForChildClasses)) {
                $ret[] = $childData;
            }
            if ($this->_hasChildComponentClass($childData->componentClass, $lookingForChildClasses)) {
//echo "<h3>SCHON: ".$childData->componentClass."</h3>";
                $ret = array_merge($ret, $this->_getComponentsByClassRek($lookingForChildClasses, $childData));
//} else {
//echo "<h3>NED: ".$childData->componentClass."</h3>";
            }
        }
        return $ret;
    }
    
    //$this->_hasChildComponentCache muss vor erstem aufruf mit anderen
    //klassen in $lookingForChildClasses geleert werden
    //fkt ist aber eh private
    private function _hasChildComponentClass($class, $lookingForChildClasses)
    {
        if (isset($this->_hasChildComponentCache[$class])) {
            return $this->_hasChildComponentCache[$class];
        }
        if (in_array($class, $lookingForChildClasses)) {
            $this->_hasChildComponentCache[$class] = true;
            return true;
        }
        $childClasses = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        $this->_hasChildComponentCache[$class] = false;
        foreach ($childClasses as $c) {
            if ($c) {
                if ($this->_hasChildComponentClass($c, $lookingForChildClasses)) {
                    $this->_hasChildComponentCache[$class] = true;
                    return true;
                }
            }
        }
        return false;
    }
    
    public function getComponentByClass($class)
    {
        $components = $this->getComponentsByClass($class);
        if (isset($components[0])) {
            return $components[0];
        }
        return null;
    }
}
?>