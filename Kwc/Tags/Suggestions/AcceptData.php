<?php
class Kwc_Tags_Suggestions_AcceptData extends Kwf_Data_Abstract
{
    public function save(Kwf_Model_Row_Interface $row, $data)
    {
        if ($data) {
            $row->status = 'accepted';
        }
    }

    public function load($row)
    {
        return false;
    }
}
