<?php
class Vpc_Admin
{
    protected $_db;

    protected function __construct(Zend_Db_Adapter_Pdo_Mysql $db)
    {
        $this->_db = $db;
    }

    public function getControllerConfig($component)
    {
        return array();
    }

    public function getControllerClass()
    {
        return 'Vps.Auto.FormPanel';
    }

    // ****************
    public function getConfig(Vpc_Abstract $component, $config = array(), $includeClass = true)
    {
        $admin = Vpc_Admin::getInstance($component);
        $config = array_merge($config, $admin->getControllerConfig($component));
        if (!isset($config['controllerUrl'])) {
            $config['controllerUrl'] = $admin->getControllerUrl($component);
        }
        if ($includeClass) {
            $return['config'] = $config;
            $return['class'] = $admin->getControllerClass($component);
            return $return;
        } else {
            return $config;
        }
    }

    public function getNoAdminConfig(Vpc_Abstract $component, $controllerClass, $config = array())
    {
        $admin = Vpc_Admin::getInstance($component);
        $config['controllerUrl'] = $admin->getControllerUrl($component);
        $return['config'] = $config;
        $return['class'] = $controllerClass;
        return $return;
    }

    public function getControllerUrl(Vpc_Abstract $component, $class = '')
    {
        if ($class == '') { $class = get_class($component); }
        if (substr($class, -10) == 'Controller') {
            $class = substr($class, 0, -10);
        }
        return '/component/edit/' . $class . '/' . $component->getId() . '/';
    }

    // ****************

    public function setup() {}

    public function delete(Vpc_Abstract $component)
    {
        $row = $this->_getRow($component);
        if ($row) {
            $row->delete();
        }
        foreach ($component->getChildComponents() as $cc) {
            Vpc_Admin::getInstance($component)->delete($component);
        }
    }

    public function duplicate($component) {}

    protected function _getRow(Vpc_Abstract $component)
    {
        $where = array();
        $where['page_id = ?'] = $component->getDbId();
        $where['component_key = ?'] = $component->getComponentKey();
        return $component->getTable()->fetchAll($where)->current();
    }

    public function copyTemplate($source, $target)
    {
        $source = VPS_PATH . str_replace('_', '/', substr(get_class($this), 0, strrpos(get_class($this), '_'))) . '/' . $source;
        if (is_file($source)) {
            $target = 'application/views/' . $target;
            if (!is_file($target)){
                if (!is_dir(dirname($target))) {
                    mkdir(dirname($target));
                }
                return copy($source, $target);
            }
        }
        return false;
    }

    static public function getInstance($componentClass)
    {
        if ($componentClass instanceof Vpc_Abstract) {
            $componentClass = get_class($componentClass);
        }
        if (class_exists($componentClass) &&
            class_exists('Vpc_Abstract') &&
            is_subclass_of($componentClass, 'Vpc_Abstract'))
        {
            $class = $componentClass;
            while ($class != 'Vpc_Abstract') {
                $len = strlen(strrchr($class, '_'));
                $setupClass = substr($class, 0, -$len) . '_Admin';
                try {
                    if (class_exists($setupClass)) {
                        return new $setupClass(Zend_Registry::get('dao')->getDb());
                    }
                } catch (Zend_Exception $e) {
                }
                $class = get_parent_class($class);
            }
        }
        return null;
    }

    function createTable($tablename, $fields)
    {
        if (!$this->_tableExists($tablename)) {
            $f = array();
            $f['page_id'] = 'int(10) unsigned NOT NULL';
            $f['component_key'] = 'varchar(255) NOT NULL';
            $f = array_merge($f, $fields);

            $sql = "CREATE TABLE `$tablename` (";
            foreach ($f as $field => $data) {
                $sql .= " `$field` $data," ;
            }
            $sql .= 'PRIMARY KEY (page_id, component_key))';
            $sql .= 'ENGINE=InnoDB DEFAULT CHARSET=utf8';
            $this->_db->query($sql);
            return true;
        }
        return false;
    }

    protected function _tableExists($tablename)
    {
        return in_array($tablename, $this->_db->listTables());
    }

    public static function getAvailableComponents($path = '')
    {
        if ($path == '') {
            $path = VPS_PATH . 'Vpc/';
        }
        $return = array();
        foreach (new DirectoryIterator($path) as $item) {
            if ($item->getFilename() != '.' && $item->getFilename() != '..' && $item->getFilename() != '.svn'){
                if ($item->isDir()){
                    $pathNew = "$path$item/";
                    $return = array_merge(self::getAvailableComponents($pathNew), $return);
                } else {
                    if (substr($item->getFilename(), -4) == '.php') {
                        $component = str_replace('/', '_', $item->getPath());
                        $component = strrchr($component, 'Vpc_');
                        $component .= '_' . str_replace('.php', '', $item->getFilename());
                        if (is_subclass_of($component, 'Vpc_Abstract')) {
                            try {
                                $name = constant("$component::NAME");
                                $return[$name] = $component;
                            } catch (Vps_CustomException $e) {
                            }
                        }
                    }
                }
            }
        }
        return $return;
    }

}