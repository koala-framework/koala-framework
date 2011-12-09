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

    public function getCacheMeta()
    {
        $dir = $this->getData()->parent->getComponent()->getItemDirectory();
        if (is_string($dir)) {
            $c = Kwc_Abstract::getComponentClassByParentClass($dir);
            $generator = Kwf_Component_Generator_Abstract::getInstance($c, 'detail');
        } else {
            $generator = $dir->getGenerator('detail');
        }
        $ret = parent::getCacheMeta();
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model($generator->getModel(), "{component_id}-feed");
        return $ret;
    }

    protected function _getRssEntryByItem(Kwf_Component_Data $item)
    {
        return array(
            'title' => $item->getTitle(),
            'description' => '',
            'link' => 'http://'.$_SERVER['HTTP_HOST'].$item->url //TODO this will be cached and that will cause problems
        );
    }

    protected function _getRssTitle()
    {
        return parent::_getRssTitle().' - '.trlKwf('Feed');
    }

    public function getViewCacheLifetime()
    {
        return 60*60;
    }
}
