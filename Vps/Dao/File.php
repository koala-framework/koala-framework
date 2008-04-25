<?php
class Vps_Dao_File extends Vps_Db_Table
{
    protected $_name = 'vps_uploads';
    protected $_rowClass = 'Vps_Dao_Row_File';
    
    public function clean()
    {
        foreach ($this->fetchAll() as $file) {
            if (!is_file($file->getFileSource())) {
                $file->delete();
            }
        }
    }
}
