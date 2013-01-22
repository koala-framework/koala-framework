<?php
class Kwc_Articles_Directory_TagSuggestionsDenyData extends Kwf_Data_Abstract
{
    public function save(Kwf_Model_Row_Interface $row, $data)
    {
        if ($data) {
            $row->status = 'denied';
            $row->getParentRow('ArticleToTag')->delete();
            $row->article_to_tag_id = null;
            $row->save();
        }
    }

    public function load($row)
    {
        return false;
    }
}
