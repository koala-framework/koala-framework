<?php
class Kwc_Root_TrlRoot_SiblingModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_trl_languages';
    protected $_referenceMap = array(
        'sibling' => array(
            'refModelClass' => 'Kwc_Root_TrlRoot_Model',
            'column' => 'id'
        )
    );
}
