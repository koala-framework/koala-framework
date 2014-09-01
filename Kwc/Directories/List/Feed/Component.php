<?php
class Kwc_Directories_List_Feed_Component extends Kwc_Abstract_Feed_Component
{
    protected function _getSelect()
    {
        return $this->getData()->parent->getComponent()->getSelect();
    }

    protected function _getRssEntries()
    {
        $select = $this->_getSelect();
        if (!$select) return array();
        $select->limit(10);
        if (!$select->hasPart('group')) {
            $select->group('id');
        }

        $itemDirectory = $this->getData()->parent->getComponent()->getItemDirectory();
        if (is_string($itemDirectory)) {
            $c = Kwc_Abstract::getComponentClassByParentClass($itemDirectory);
            $generator = Kwf_Component_Generator_Abstract::getInstance($c, 'detail');
            $items = $generator->getChildData(null, $select);
            //TODO: callModifyItemData aufrufen
        } else {
            $items = $itemDirectory->getChildComponents($select);
            foreach ($items as $item) {
                $itemDirectory->getComponent()->callModifyItemData($item);
            }
        }

        $ret = array();
        foreach ($items as $item) {
            $ret[] = $this->_getRssEntryByItem($item);
        }

        return $ret;
    }

    protected function _getRssEntryByItem(Kwf_Component_Data $item)
    {
        return array(
            'title' => $item->getTitle(),
            'description' => '',
            'link' => $item->getAbsoluteUrl(),
            'guid' => $item->id
        );
    }

    protected function _getRssTitle()
    {
        return parent::_getRssTitle().' - '.trlKwf('Feed');
    }
}
