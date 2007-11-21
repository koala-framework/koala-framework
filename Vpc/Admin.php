<?php
class Vpc_Admin
{
    protected $_db;
    protected $_class;

    protected function __construct($class, Zend_Db_Adapter_Pdo_Mysql $db)
    {
        $this->_class = $class;
        $this->_db = $db;
    }

    public static function getInstance($componentClass)
    {
        if (class_exists($componentClass) &&
            class_exists('Vpc_Abstract') &&
            is_subclass_of($componentClass, 'Vpc_Abstract'))
        {
            $class = $componentClass;
            while ($class != 'Vpc_Abstract') {
                $len = strlen(strrchr($class, '_'));
                $setupClass = substr($class, 0, -$len) . '_Admin';
//                 try {
                    if (class_exists($setupClass)) {
                        return new $setupClass($componentClass, Zend_Registry::get('db'));
                    }
//                 } catch (Zend_Exception $e) {
//                 }
                $class = get_parent_class($class);
            }
        }
        return null;
    }

    public static function getAvailableComponents($path = '')
    {
        if ($path == '') {
            $path = VPS_PATH . '/Vpc';
        }
        $return = array();
        foreach (new DirectoryIterator($path) as $item) {
            if ($item->getFilename() != '.' &&
                $item->getFilename() != '..' &&
                $item->getFilename() != '.svn' &&
                $item->isDir()
            ){
                
                $pathNew = "$path/$item";
                $return = array_merge(self::getAvailableComponents($pathNew), $return);
                
            } else if (substr($item->getFilename(), -4) == '.php') {
                
                $class = str_replace('/', '_', $item->getPath());
                $class = strrchr($class, 'Vpc_');
                $class .= '_' . str_replace('.php', '', $item->getFilename());
                if (class_exists($class) && is_subclass_of($class, 'Vpc_Abstract')) {
                    $return[] = $class;
                }
                
            }
        }
        return $return;
    }

    // ****************
    
    public static final function getConfig($class, $pageId = null, $componentKey = null, $config = array())
    {
        $admin = Vpc_Admin::getInstance($class);
        $adminConfig = $admin->getControllerConfig($pageId, $componentKey);
        $config = array_merge($config, $adminConfig);
        $controllerClass = $admin->getControllerClass();
        $controllerUrl = $admin->getControllerUrl();
        return Vpc_Admin::createConfig($controllerClass, $controllerUrl, $config, $pageId, $componentKey);
    }

    public static final function createConfig($controllerClass, $controllerUrl, $config = array(), $pageId = null, $componentKey = null)
    {
        if (!is_array($config)) { $config = array(); }
        if (!isset($config['controllerUrl'])) {
            $config['controllerUrl'] = $controllerUrl;
        }
        if ($pageId) {
            if (!isset($config['baseParams'])) {
                $config['baseParams'] = array();
            }
            $config['baseParams']['page_id'] = $pageId;
            $config['baseParams']['component_key'] = $componentKey;
        }
        $return['config'] = $config;
        $return['class'] = $controllerClass;
        return $return;
    }

    // ****************
    
    public function getControllerClass()
    {
        return 'Vps.Auto.FormPanel';
    }

    public function getControllerConfig()
    {
        return array();
    }

    public function getControllerUrl($class = null)
    {
        if (is_null($class)) $class = $this->_class;
        if (substr($class, -10) == 'Controller') {
            $class = substr($class, 0, -10);
        }
        return '/admin/component/edit/' . $class;
    }

    // ***************
    
    protected function _getRow($pageId, $componentKey)
    {
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        if ($tablename) {
            $table = new $tablename(array('componentClass'=>$this->_class));
            return $table->find($pageId, $componentKey)->current();
        }
        return null;
    }

    protected function _getRows($pageId, $componentKey)
    {
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        if ($tablename) {
            $table = new $tablename();
            $where = array(
                'page_id = ?' => $pageId,
                'component_key = ?' => $componentKey
            );
            return $table->fetchAll($where);
        }
        return array();
    }

    public function setup() {}

    public function delete($pageId, $componentKey)
    {
        $row = $this->_getRow($pageId, $componentKey);
        if ($row) {
            $row->delete();
        }
    }

    public function duplicate($component) {}

    function createFormTable($tablename, $fields)
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

            if (isset($fields['vps_upload_id'])) {
                $this->_db->query("ALTER TABLE $tablename
                    ADD INDEX (vps_upload_id)");
                $this->_db->query("ALTER TABLE $tablename
                    ADD FOREIGN KEY (vps_upload_id)
                    REFERENCES vps_uploads (id)
                    ON DELETE RESTRICT ON UPDATE RESTRICT");
            }
            return true;
        }
        return false;
    }

    protected function _tableExists($tablename)
    {
        return in_array($tablename, $this->_db->listTables());
    }

    public static function getComponentFile($class, $filename = '', $ext = 'php', $returnClass = false)
    {
        $ret = null;
        $retClass = null;
        while (!$ret && $class != '') {
            $curClass = $class;
            if ($filename == '') {
                $filename = substr(strrchr($curClass, '_'), 1);
            }
            $curClass = substr($class, 0, strrpos($class, '_') + 1) . $filename;
            $file = str_replace('_', DIRECTORY_SEPARATOR, $curClass) . '.' . $ext;
            $dirs = explode(PATH_SEPARATOR, get_include_path());
            foreach ($dirs as $dir) {
                if ($dir == '.') { $dir = getcwd(); }
                $path = $dir . '/' . $file;
                if (is_file($path)) {
                    $ret = $path;
                    $retClass = $curClass;
                    break;
                }
            }
            $class = get_parent_class($class);
        }
        return $returnClass ? $retClass : $ret;
    }

}