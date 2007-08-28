<?php
class Vpc_Setup_Abstract
{
    protected $_standard = array( "page_id" => "int(10) unsigned NOT NULL",
                                  "component_key" => "varchar(255) NOT NULL");

    protected $_keys = array('page_id', 'component_key');

    protected $_db;


    public function __construct(Zend_Db_Adapter_Pdo_Mysql $db){
        $this->_db = $db;
    }

    public function createInstance($componentClass)
    {
        if (is_subclass_of($componentClass, 'Vpc_Abstract')) {
            $class = $componentClass;
            while ($class != 'Vpc_Abstract') {
                $len = strlen(strrchr($class, '_'));
                $setupClass = substr($class, 0, -$len) . '_Setup';
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
    

    function createTable ($name, $fields){
        if (!$this->_tableExits($name)) {


            $allFields = array_merge($this->_standard, $fields);

            $sql = "CREATE TABLE `$name` (";

            //add fields
            foreach ($allFields as $field => $data) {
                $sql .= " `$field` $data," ;
            }

            //add keys
            $cnt = 0;
            $sql .= " PRIMARY KEY  (";
            //PRIMARY KEY  (`id`,`page_key`,`component_key`)
            foreach ($this->_keys as $key){
            if ($cnt != 0){
                    $sql .= ", ";
                }
                $sql .= "`$key`";
                $cnt++;
            }
             //add end of statement
            $sql .= ")) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            $this->_db->query($sql);
        }
    }


    protected function _tableExits ($tablename){
        $tableList = $this->_db->listTables();
        return in_array($tablename, $tableList);
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
    
    public function copyTemplate($path)
    {
        $path = 'application/views/' . $path;
        if (!file_exists($path)){
            copy(VPS_PATH . '/' . $path, $path);
        }
    }

    public function setup() {}
    public function deleteEntry($pageId, $componentKey) {}
}