<?php
class Kwc_Tags_Suggestions_DenyData extends Kwf_Data_Abstract
{
    public function save(Kwf_Model_Row_Interface $row, $data)
    {
        if ($data) {
            $parentRow = $row->getParentRow('ComponentToTag');
            $row->status = 'denied';
            $row->component_to_tag_id = null;
            $row->save();
            $parentRow->delete();
        }
    }

    public function load($row)
    {
        return false;
    }
}
