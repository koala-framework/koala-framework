<?php
class Vpc_Basic_Flash_FlashVarsModel extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_basic_flash_vars';
    protected $_referenceMap = array(
        'FlashModel' => array(
            'refModelClass' => 'Vpc_Basic_Flash_Model',
            'column' => 'parent_id'
        )
    );
}
