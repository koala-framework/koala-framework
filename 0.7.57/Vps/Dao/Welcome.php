<?php
class Vps_Dao_Welcome extends Vps_Db_Table
{
    protected $_name = 'vps_welcome';
    protected $_rowClass = 'Vps_Dao_Row_Welcome';
    protected $_referenceMap    = array(
        'WelcomeImage' => array(
            'columns'           => array('vps_upload_id'),
            'refTableClass'     => 'Vps_Dao_File',
            'refColumns'        => array('id')
        ),
        'LoginImage' => array(
            'columns'           => array('login_vps_upload_id'),
            'refTableClass'     => 'Vps_Dao_File',
            'refColumns'        => array('id')
        )
    );
}
