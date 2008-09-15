<?php
class Vpc_Forum_Directory_Model extends Vps_Db_Table_Abstract
{
    protected $_name = 'vpc_forum_groups';
    protected $_rowClass = 'Vpc_Forum_Directory_Row';
    protected $_autoFill = array(
        'cache_child_component_id' => '{component_id}_{id}'
    );
}
