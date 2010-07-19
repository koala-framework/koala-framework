<?php
class Vps_Component_Cache_Mysql_PreloadModel extends Vps_Model_Db_Proxy
{
    protected $_table = 'cache_componentpreload';
    protected $_referenceMap = array(
        'cache' => array(
            'column' => 'page_id',
            'refModelClass' => 'Vps_Component_Cache_Mysql_Model'
        )
    );
}
