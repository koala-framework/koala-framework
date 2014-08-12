<?php
class Kwc_Articles_CategorySimple_List_Events extends Kwc_Abstract_Composite_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (call_user_func(array($this->_class, 'getItemDirectoryClasses'), $this->_class) as $dirCls) {
            $ret[] = array(
                'class' => $dirCls,
                'event' => 'Kwc_Directories_List_EventItemUpdated',
                'callback' => 'onArticleChanged'
            );
        }
        $modelName = Kwc_Abstract::getSetting(
            Kwc_Abstract::getSetting($this->_class, 'categoryComponentClass'), 'categoryToItemModelName'
        );
        $ret[] = array(
            'class' => $modelName,
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onCategoryChanged'
        );
        $ret[] = array(
            'class' => $modelName,
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onCategoryChanged'
        );
        $ret[] = array(
            'class' => $modelName,
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onCategoryUpdated'
        );
        return $ret;
    }

    private function _deleteCache($dbId)
    {
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId) as $c) {
            foreach (array('intern', 'external') as $i) {
                $cacheId = 'articleCategoryCount'.$c->componentId.$i;
                Kwf_Cache_Simple::delete($cacheId);
            }
        }
    }

    public function onArticleChanged(Kwc_Directories_List_EventItemAbstract $ev)
    {
        $model = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting(
            Kwc_Abstract::getSetting($this->_class, 'categoryComponentClass'), 'categoryToItemModelName'
        ));
        $categoryIds = array();
        foreach ($model->getRows($model->select()->whereEquals('item_id', $ev->itemId)) as $row) {
            $categoryIds[] = $row->category_id;
        }
        $this->_onCategoryChanged(array_unique($categoryIds));
    }

    public function onCategoryChanged(Kwf_Events_Event_Row_Abstract $ev)
    {
        $this->_onCategoryChanged($ev->row->category_id);
    }

    private function _onCategoryChanged($categoryIds)
    {
        $model = Kwc_Abstract::createOwnModel($this->_class);
        $select = $model->select()->whereEquals('category_id', $categoryIds);
        foreach ($model->getRows($select) as $row) {
            $this->_deleteCache($row->component_id);
        }
    }

    public function onCategoryUpdated(Kwf_Events_Event_Row_Updated $ev)
    {
        $this->_deleteCache($ev->row->component_id);
    }
}
