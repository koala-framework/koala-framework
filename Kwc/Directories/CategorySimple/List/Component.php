<?php
abstract class Kwc_Directories_CategorySimple_List_Component extends Kwc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['categoryComponentClass'] = 'Kwc_Directories_CategorySimple_Component';
        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();

        $model = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting(
            $this->_getSetting('categoryComponentClass'), 'categoryToItemModelName'
        ));
        $s = $model->select()->whereEquals('category_id', $this->getRow()->category_id);
        $itemIds = array();
        foreach ($model->getRows($s) as $row) {
            $itemIds[] = $row->item_id;
        }
        $select->whereEquals('id', $itemIds);
        $select->order('date', 'DESC');
        $select->order('priority', 'DESC');
        return $select;
    }
}
