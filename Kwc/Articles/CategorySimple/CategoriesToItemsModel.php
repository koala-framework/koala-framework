<?php
class Kwc_Articles_CategorySimple_CategoriesToItemsModel
    extends Kwc_Directories_CategorySimple_CategoriesToItemsModel
{
    protected function _init()
    {
        $this->_referenceMap['Item'] = 'item_id->Kwc_Articles_Directory_Model';
        parent::_init();
    }
}
