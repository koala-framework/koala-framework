<?php
class Vps_Dao_File extends Vps_Db_Table
{
    protected $_name = 'vps_uploads';
    protected $_rowClass = 'Vps_Dao_Row_File';
    protected $_dependentTables = array('Vpc_Basic_Image_Model', 'Vpc_Basic_DownloadTag_Model');
}
