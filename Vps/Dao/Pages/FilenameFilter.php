<?php
class Vps_Dao_Pages_FilenameFilter extends Vps_Filter_Row_UniqueAscii
{
    public function __construct($sourceField = null)
    {
        parent::__construct($sourceField);
        $this->setGroupBy(array('parent_id'));
    }

    public function skipFilter($row)
    {
        if ($row->custom_filename) return true;
        return parent::skipFilter($row);
    }
}
