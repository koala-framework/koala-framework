<?php
class Vpc_Root_TrlRoot_SiblingModel extends Vps_Model_Db
{
    protected $_table = 'kwc_trl_languages';
    protected $_referenceMap = array(
        'sibling' => array(
            'refModelClass' => 'Vpc_Root_TrlRoot_Model',
            'column' => 'id'
        )
    );
}
