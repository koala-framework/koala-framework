<?php
abstract class Kwc_Directories_CategorySimple_List_Component extends Kwc_Directories_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Directories_CategorySimple_List_Model';
        $ret['categoryComponentClass'] = 'Kwc_Directories_CategorySimple_Component';
        return $ret;
    }

    public function getSelect()
    {
        if (!$this->getRow()->category_id) {
            throw new Kwf_Exception_Client(trlKwf('No category selected.'));
        }
        $model = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting(
            $this->_getSetting('categoryComponentClass'), 'categoryToItemModelName'
        ));
        $itemIds = array();
        $s = $model->select()
            ->whereEquals('category_id', $this->getRow()->getParentRow('Category')->getRecursiveIds());
        foreach ($model->getRows($s) as $row) {
            $itemIds[] = $row->item_id;
        }

        $select = parent::getSelect();
        $select->whereEquals('id', array_unique($itemIds));
        $select->order('date', 'DESC');
        $select->order('priority', 'DESC');
        return $select;
    }
}
