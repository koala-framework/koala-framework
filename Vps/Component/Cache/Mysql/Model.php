<?php
class Vps_Component_Cache_Mysql_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'cache_component';
    protected $_dependentModels = array('preload' => 'Vps_Component_Cache_Mysql_PreloadModel');
}
