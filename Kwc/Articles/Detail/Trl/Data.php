<?php
class Kwc_Articles_Detail_Trl_Data extends Kwf_Data_Trl_OriginalComponentFromData
{
    public function load($row)
    {
        if ($this->getFieldname() == 'author_id') {
            $select = new Kwf_Model_Select();
            $select->whereEquals('id', $this->_getChainedRow($row)->author_id);
            return Kwf_Model_Abstract::getInstance('Kwc_Articles_Directory_AuthorsModel')->getRow($select)->name;
        }
        parent::load($row);
    }
}
