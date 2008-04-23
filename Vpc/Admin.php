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
        static $instances = array();
        if (!isset($instances[$componentClass])) {
            $c = Vpc_Admin::getComponentFile($componentClass, 'Admin', 'php', true);
            $instances[$componentClass] = new $c($componentClass, Zend_Registry::get('db'));
        }
        return $instances[$componentClass];
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
                $item->isDir()) {

                $pathNew = "$path/$item";
                $return = array_merge(self::getAvailableComponents($pathNew), $return);

            } else if (substr($item->getFilename(), -4) == '.php') {

                $class = str_replace('/', '_', $item->getPath());
                $class = strrchr($class, 'Vpc_');
                $class .= '_' . str_replace('.php', '', $item->getFilename());
                if (Vps_Loader::classExists($class) && is_subclass_of($class, 'Vpc_Abstract')) {
                    $return[] = $class;
                }

            }
        }
        return $return;
    }

    public function getExtConfig()
    {
        $name = Vpc_Abstract::getSetting($this->_class, 'componentName');
        $icon = Vpc_Abstract::getSetting($this->_class, 'componentIcon');
        return array(
            'xtype'         => 'vps.autoform',
            'controllerUrl' => $this->getControllerUrl(),
            'componentName' => $name,
            'componentIcon' => $icon->__toString()
        );
    }

    public function getControllerUrl($class = null)
    {
        if (is_null($class)) $class = $this->_class;
        if (substr($class, -10) == 'Controller') {
            $class = substr($class, 0, -10);
        }
        return '/admin/component/edit/' . $class;
    }

    protected function _getRow($componentId)
    {
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        if ($tablename) {
            $table = new $tablename(array('componentClass'=>$this->_class));
            return $table->find($componentId)->current();
        }
        return null;
    }

    protected function _getRows($componentId)
    {
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename');
        if ($tablename) {
            $table = new $tablename(array('componentClass' => $this->_class));
            $where = array(
                'component_id = ?' => $componentId
            );
            return $table->fetchAll($where);
        }
        return array();
    }

    public function setup()
    {
    }

    public function delete($componentId)
    {
        $row = $this->_getRow($componentId);
        if ($row) {
            $row->delete();
        }
    }

    public function duplicate($component)
    {
    }

    function createFormTable($tablename, $fields)
    {
        if (!$this->_tableExists($tablename)) {
            $f = array();
            $f['component_id'] = 'varchar(255) NOT NULL';
            $f = array_merge($f, $fields);

            $sql = "CREATE TABLE `$tablename` (";
            foreach ($f as $field => $data) {
                $sql .= " `$field` $data," ;
            }
            $sql .= 'PRIMARY KEY (component_id))';
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

    public static function getComponentClass($class, $filename)
    {
        return self::getComponentFile($class, $filename, 'php', true);
    }

    public static function getComponentFile($class, $filename = '', $ext = 'php', $returnClass = false)
    {
        if (is_object($class)) $class = get_class($class);

        if (!class_exists($class) || !is_subclass_of($class, 'Vpc_Interface')) {
            throw new Vps_Exception("Übergegeben Klasse '$class' existiert nicht "
                ."oder ist keine Komponente.");
        }

        $ret = null;
        while (!$ret && $class != '') {
            $curClass = $class;
            if ($filename != '') {
                $curClass = substr($class, 0, strrpos($class, '_') + 1) . $filename;
            }
            $file = str_replace('_', DIRECTORY_SEPARATOR, $curClass) . '.' . $ext;
            $dirs = explode(PATH_SEPARATOR, get_include_path());
            foreach ($dirs as $dir) {
                if ($dir == '.') $dir = getcwd();
                $path = $dir . '/' . $file;
                if (is_file($path)) {
                    $ret = $returnClass ? $curClass : $path;
                    break;
                }
            }
            $class = get_parent_class($class);
        }
        return $ret;
    }
}
