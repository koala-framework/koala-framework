<?php
class Vpc_Basic_LinkTag_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_basic_linktag';
    protected $_rowClass = 'Vpc_Basic_LinkTag_Row';
    protected $_default = array('component'=>'intern');
}
