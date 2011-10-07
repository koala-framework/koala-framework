<?php
class Vpc_Abstract_Cards_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_basic_cards';
    protected $_rowClass = 'Vpc_Abstract_Cards_Row';
    protected $_toStringField = 'component';
}
