<?php
class Vpc_Setup_Abstract 
{
    protected $_standard = array(   "component_id" => "int(10) unsigned NOT NULL", 
                                  "page_key" => "varchar(255) NOT NULL", 
                                  "component_key" => "varchar(255) NOT NULL");
                                  
    protected $_keys = array('component_id', 'page_key', 'component_key');
                                                
    protected $_db;
    
    
    public function __construct(Zend_Db_Adapter_Pdo_Mysql $db){
        $this->_db = $db;
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
        $path = 'Vpc/' . $path;
        $return = array();
        if (is_dir($path)) {
            $return = array_merge($return, self::_getAvailableComponents($path));
        }
        if (is_dir(VPS_PATH . $path)) {
            $return = array_merge($return, self::_getAvailableComponents(VPS_PATH . $path));
        }
        return $return;
    }
    
    private static function _getAvailableComponents($path)
    {
        $return = array();
        foreach (new DirectoryIterator($path) as $item) {
            if ($item->getFilename() != '.' && $item->getFilename() != '..' && $item->getFilename() != '.svn'){
                if ($item->isDir()){
                    $pathNew = "$path$item/";
                    $return = array_merge(self::_getAvailableComponents($pathNew), $return);
                } else {
                    if ($item->getFilename() == 'Index.php'){
                        $component = str_replace('/', '_', $item->getPath());
                        $component = strrchr($component, 'Vpc_');
                        $component .= '_Index';             
                        $return[] = $component;
                    }
                }
            }
        }
        return $return;
    }
    
}